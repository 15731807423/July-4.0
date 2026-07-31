<?php

namespace Translate\Controllers\Concerns;

use Illuminate\Http\Request;
use Translate\Translate;

trait ValidatesTranslationInput
{
    protected function translationNodeIds(Request $request): ?array
    {
        $nodes = $request->input('nodes');
        if (!is_array($nodes)) {
            return null;
        }

        $ids = [];
        foreach ($nodes as $node) {
            if (!is_int($node) && !is_string($node)) {
                return null;
            }
            if (filter_var($node, FILTER_VALIDATE_INT) === false || (int) $node < 1) {
                return null;
            }
            $ids[] = (int) $node;
        }

        return array_values(array_unique($ids));
    }

    protected function translationBatchTarget(Request $request): ?string
    {
        $code = $request->input('code');
        if ($code === null || $code === '') {
            return Translate::legacyBatchTargetCode();
        }

        return is_string($code) && Translate::isTargetCode($code) ? $code : null;
    }

    protected function translationTarget(Request $request): ?string
    {
        $code = $request->input('code');
        return is_string($code) && Translate::isTargetCode($code) ? $code : null;
    }

    protected function translationText(Request $request): ?array
    {
        $text = $request->input('text');
        if (is_string($text)) {
            $text = json_decode($text, true);
        }

        if (!is_array($text) || !$text || array_is_list($text)) {
            return null;
        }

        foreach ($text as $key => $value) {
            if (!is_string($key) || !is_string($value)) {
                return null;
            }
        }

        return $text;
    }

    protected function translationTaskData(Request $request): ?string
    {
        $data = $request->input('data');
        if (!is_string($data) || $data === '') {
            return null;
        }

        $decoded = json_decode($data, true);
        return is_array($decoded) && $decoded && !array_is_list($decoded)
            ? $data
            : null;
    }
}
