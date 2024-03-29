<?php

namespace Larapress\Profiles\Services\FormEntry\FormContentProviders;

use Larapress\Profiles\Models\Form;
use Larapress\Profiles\Services\FormEntry\IFormContentProvider;
use Illuminate\Support\Str;

class VuetifyFormContentProvider implements IFormContentProvider
{
    /**
     * Undocumented function
     *
     * @return array
     */
    public function getFormRules(Form $form): array
    {
        $rules = [];
        $traverseChildren = function ($children, $traverseChildren) use (&$rules) {
            foreach ($children as $child) {
                if (isset($child['props']['rules'])) {
                    $key = $this->getVuetifyBindingKey($child['props']['v-model'] ?? null);
                    if (!is_null($key)) {
                        foreach ($child['props']['rules'] as $rule) {
                            $rules[$key] = $rule;
                        }
                    }
                }
                if (isset($child['children']) && count($child['children']) > 0) {
                    $traverseChildren($child['children'], $traverseChildren);
                }
            }
        };

        $children = $form->data['content']['children'] ?? [];
        $traverseChildren($children, $traverseChildren);

        return $rules;
    }

    /**
     * Undocumented function
     *
     * @param array $inputs
     * @return array
     */
    public function getFormValidInputs(Form $form, array $inputs): array
    {
        $validInputs = [];
        $traverseChildren = function ($children, $traverseChildren) use (&$validInputs, $inputs) {
            foreach ($children as $child) {
                if (isset($child['props']['v-model'])) {
                    $key = $this->getVuetifyBindingKey($child['props']['v-model'] ?? null);
                    if (!is_null($key) && isset($inputs[$key])) {
                        $validInputs[$key] = $inputs[$key];
                    }
                }
                if (isset($child['children']) && count($child['children']) > 0) {
                    $traverseChildren($child['children'], $traverseChildren);
                }
            }
        };

        $children = $form->data['content']['children'] ?? [];
        $traverseChildren($children, $traverseChildren);

        return $inputs;
    }

    protected function getVuetifyBindingKey($key)
    {
        if (!is_null($key)) {
            if (Str::startsWith($key, '$(bindings.')) {
                $len = Str::length('$(bindings.');
                return Str::substr($key, $len, Str::length($key) - $len - 1);
            } else {
                return $key;
            }
        }
        return null;
    }
}
