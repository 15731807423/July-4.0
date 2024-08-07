<?php

namespace July\Node;

use App\Models\ModelBase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NodeIndex extends ModelBase
{
    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'node_index';

    /**
     * 可批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = [
        'entity_id',
        'field_id',
        'content',
        'langcode',
        'weight',
    ];

    /**
     * 存放经关键词分拆后的令牌
     *
     * @var array
     */
    protected $tokens = [];

    /**
     * 重建索引
     *
     * @return bool
     */
    public static function rebuild()
    {
        DB::beginTransaction();

        DB::delete('DELETE FROM node_index;');

        // 索引标题
        Node::all(['id','title','langcode'])->each(function(Node $node) {
            DB::table('node_index')->insert([
                'entity_id' => $node->getKey(),
                'field_id' => 'title',
                'content' => trim($node->title),
                'langcode' => $node->langcode,
                'weight' => 10,
            ]);
        });

        NodeTranslation::all(['entity_id','title','langcode'])->each(function(NodeTranslation $node) {
            DB::table('node_index')->insert([
                'entity_id' => $node->entity_id,
                'field_id' => 'title',
                'content' => trim($node->title),
                'langcode' => $node->langcode,
                'weight' => 10,
            ]);
        });


        // 索引其它字段
        NodeField::searchable()->each(function (NodeField $field) {
            foreach (static::extractValueIndex($field) as $record) {
                DB::table('node_index')->insert($record);
            }
        });

        DB::commit();

        return true;
    }

    /**
     * 将指定字段的值转化为索引记录
     *
     * @param  \July\Node\NodeField $field
     * @return array
     */
    protected static function extractValueIndex(NodeField $field)
    {
        // return $field->getValuesAll();

        $values = [];
        $fieldId = $field->getKey();
        $fieldType = $field->getFieldType();
        $weight = $field->weight;

        foreach ($field->getValueRecords() as $record) {
            $values[] = [
                'entity_id' => $record['entity_id'],
                'field_id' => $fieldId,
                'content' => $fieldType->toIndex($record['value']),
                'langcode' => $record['langcode'],
                'weight' => $weight,
            ];
        }

        return $values;
    }

    /**
     * 在索引中检索指定的关键词
     *
     * @param  string $keywords 待检索的关键词
     * @param  string $lang 语言代码
     * @return array
     */
    public static function search($keywords, $lang)
    {
        $key = $keywords;

        if (empty($keywords)) {
            return [
                'keywords' => $keywords,
                'results' => [],
            ];
        }

        // 处理关键词
        $keywords = static::normalizeKeywords($keywords);

        $h1 = \Illuminate\Support\Facades\Schema::hasTable('node__h1');


        // 获取搜索结果
        $results = [];
        foreach (static::searchIndex($keywords, $lang) as $result) {

            $node_id = $result->entity_id;
            $field_id = $result->field_id;
            $result = $result->toSearchResult($keywords);

            if ($result === false) {
                continue;
            }

            if (! isset($results[$node_id])) {
                $results[$node_id] = [
                    'node_id' => $node_id,
                    'weight' => 0,
                ];
            }
            $str = $h1 ? DB::table('node__h1')->where('entity_id',$node_id)->value('h1') : null;
            $ss = DB::table('entity_path_aliases')->where('entity_id',$node_id)->value('entity_id');
            // foreach ($ss  as $key => $value) {
                // Log::info( $ss);
            // }
            if(stripos($str,$key)!== false){
                $index =  stripos($str,$key);
                $length = mb_strlen($key);

                $abs =  substr($str,$index,$length);
                $results[$node_id]['h1'] = str_ireplace($key,"<span class= \"keyword\">".$abs."</span>" , $str);
            }

            $results[$node_id][$field_id] =  $result['content'];
            $results[$node_id]['weight'] +=  $result['weight'];
            //过滤掉没有url的
            if($ss != $node_id){
                unset($results[$node_id]);
            }
        }

        // 对结果排序
        array_multisort(
            array_column($results, 'weight'),
            SORT_DESC,
            array_column($results, 'node_id'),
            SORT_NUMERIC,
            $results
        );
        return [
            'keywords' => key($keywords),
            'results' => $results,
        ];
    }

    /**
     * 在索引中检索指定的关键词
     *
     * @param  array $keywords 关键词
     * @param  string|null $lang
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    protected static function searchIndex(array $keywords, $lang)
    {
        $conditions = [];
        foreach ($keywords as $keyword => $weight) {
            $conditions[] = ['content', 'like', '%'.$keyword.'%', 'or'];
        }

        $not = static::query()->where('field_id', 'title')->where('content', '404')->value('entity_id');

        $values = static::query()
        ->where('entity_id', '<>', $not)
        ->where('langcode', $lang)
        ->where(function ($query) use ($conditions) {
            $query->where($conditions);
        })
        ->get();
        return $values;
    }

    /**
     * 提取有效的关键词
     *
     * @param string $input
     * @return array
     */
    protected static function normalizeKeywords($input)
    {
        if (empty($input)) {
            return [];
        }

        if (strlen($input) > 100) {
            $input = substr($input, 0, 100);
        }

        $keywords = array_filter(preg_split('/\s+/', $input));
        $keywords = array_slice($keywords, 0, 10);
        $keywords = static::combineKeywords($keywords);
        arsort($keywords);
        return $keywords;
    }

    /**
     * 组合关键词，并标记权重
     *
     * @param array $keywords
     * @return array
     */
    public static function combineKeywords(array $keywords)
    {
        // 计算每个单词的权重，从左到右依次降低
        $wordWeights = [];
        foreach ($keywords as $index => $word) {
            $wordWeights[] = exp(-0.5*pow($index/3.82, 2));
        }

        // 将单词按顺序组合成查询短句，并计算每个短句的权重
        $combined = [];
        $offset = 0;
        while ($keywords) {
            $words = [];
            $weight = 0;
            foreach ($keywords as $index => $word) {
                $words[] = $word;
                $weight += $wordWeights[$offset + $index];
                $combined[implode(' ', $words)] = $weight;
            }

            $keywords = array_slice($keywords, 1);
            $offset++;
        }

        return $combined;
    }

    /**
     * 将字段值转化为一条搜索结果
     *
     * @param  array $keywords
     * @return array
     */
    public function toSearchResult(array $keywords)
    {
        // $this->tokenize($keywords);
        $tokens = $this->tokenizer($keywords);
        
        if ($tokens === false) {
            return false;
        }

        $similar = $this->similar($this->attributes['content'], strtolower(key($keywords)));
        $this->weight *= substr_count(strtolower($this->attributes['content']), strtolower(key($keywords)));

        return [
            'content' => $this->joinTokens($tokens),
            'weight' => $this->weight,
        ];
    }

    /**
     * 使用关键词将字段值令牌化
     *
     * @param  array $keywords
     * @return array
     */
    protected function tokenizer(array $keywords)
    {
        // $this->weight = 0;
        $content = trim($this->attributes['content']);
        $pattern = '/{{([\w\W]*?)}}/';
        $matches = [];
        preg_match_all($pattern, $content, $matches);
        $pattern2 = '/{%([\w\W]*?)%}/';
        $matches2 = [];
        preg_match_all($pattern2, $content, $matches2);
        $matches = array_merge($matches[0], $matches2[0]);

        foreach ($matches as $key => $value) {
            $content = str_replace($value . ';', '', $content);
            $content = str_replace($value, '', $content);
        }
        $tokens = [];
        
        if (stristr($content, strval(array_keys($keywords)[0])) === false) {
            return false;
        }

        foreach ($keywords as $keyword => $weight) {
            $keyword = (string) $keyword;
            $pos = stripos($content, $keyword);
            while ($pos !== false) {
                $tokens[] = substr($content, 0, $pos);

                $word = substr($content, $pos, strlen($keyword));
                if ($word !== $keyword) {
                    $weight *= 1 - str_diff($word, $keyword)*.5/strlen($keyword);
                }
                // $this->weight += $weight;
                $tokens[] = '<span class="keyword">'.$word.'</span>';

                $content = substr($content, $pos + strlen($keyword));
                $pos = stripos($content, $keyword);
            }
        }
        if (!empty($content)) {
            $tokens[] = $content;
        }

        return $tokens;
    }

    /**
     * 将令牌拼合在一起
     *
     * @param  array $tokens
     * @return string
     */
    protected function joinTokens(array $tokens)
    {
        $content = trim($this->attributes['content']);
        if (strlen($content) <= 200) {
            return implode('', $tokens);
        }

        $pieces = [];
        $length = 0;
        for ($i=1; $i < count($tokens); $i+=2) {
            $left = $tokens[$i-1];
            if ($left) {
                $left = explode(' ', $left);
                $left = array_slice($left, -1*min(intval(count($left)/2), 5));
                $left = implode(' ', $left);
            }

            $right = $tokens[$i+1] ?? '';
            if ($right) {
                $right = explode(' ', $right);
                $right = array_slice($right, 0, min(intval(count($right)/2), 5));
                $right = implode(' ', $right);
            }

            $piece = trim($left.$tokens[$i].$right, '.,:;!?');
            $pieces[] = $piece;
            $length += strlen($piece) - strlen('<span class="keyword"></span>');

            if ($length >= 200) {
                break;
            }
        }

        return '... '.implode(' ... ', $pieces).' ...';
    }

    /**
     * 计算两个字符串的相似度（百分比）
     *
     * @param string $str1
     * @param string $str2
     * @return float
     */
    protected function similar(string $str1, string $str2)
    {
        if ($str1 === $str2) {
            return 1;
        }

        $len1 = strlen($str1);
        $len2 = strlen($str2);
        if ($len1 === 0 || $len2 === 0) {
            return 0;
        }

        $maxlen = max($len1, $len2);
        if (strpos($str1, $str2) !== false || strpos($str2, $str1) !== false) {
            return abs($len1 - $len2) / $maxlen;
        }

        // 长度相差 3 倍以上
        if ($len1/$len2 > 3 || $len1/$len2 < 1/3) {
            return 0;
        }

        $levenshtein = levenshtein($str1, $str2);
        $levenshtein -= ($levenshtein - levenshtein(strtolower($str1), strtolower($str2))) / 2;

        return ($maxlen - $levenshtein) / $maxlen;
    }
}
