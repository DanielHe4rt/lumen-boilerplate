<?php

namespace App\FieldManagers;

abstract class FieldManager
{

    /**
     * General rules to validate this request.
     *
     * @var $rules
     */
    protected $fields = [];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules($extraRules = [])
    {
        $rules = $this->getRules();

        /* Adiciona as regras extras as regras padrão do objeto */
        foreach ($extraRules as $field => $rule) {

            if (! key_exists($this->getOnlyField($field), $rules)) {
                continue;
            }

            /* Ajuste para validação de array */
            if (strpos($field, '.') > -1) {

                $rules[$field] = $rules[$this->getOnlyField($field)] . '|' . $rule;
                unset($rules[$this->getOnlyField($field)]);
                continue;
            }

            $rules[$field] .= '|' . $rule;
        }

        return $this->transformToFrontName($rules);
    }

    /**
     * Adiciona sub campos para validação
     *
     * @param string $prefix
     * @param array $fields
     * @param array $mergeFields
     *
     * @return array
     */
    protected function merge(string $prefix, array $fields, array $mergeFields)
    {
        foreach ($mergeFields as $key => $value) {
            $fields["{$prefix}.{$key}"] = $value;
        }

        return $fields;
    }

    private function getOnlyField($string)
    {
        $array = explode('.', $string);

        return end($array);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    private function getRules()
    {
        $rules = [];
        foreach ($this->fields as $field => $extra) {
            if (! key_exists('rules', $extra)) {
                continue;
            }

            $rules[$field] = $extra['rules'];
        }

        return $rules;
    }

    /**
     * Transform attributes model.
     *
     * @return array
     */
    public function transformToResource(array $array = [])
    {
        if (count($array) < 1) {
            return $array;
        }

        $transformation = array_reverse($this->getTransformation());
        $transformed = [];

        foreach ($transformation as $name => $new_name) {
            if (! key_exists($name, $array)) {

                if (key_exists($new_name, $array)) {
                    $transformed[$name] = $array[$new_name];
                }
                continue;
            }

            $transformed[$new_name] = $array[$name];
        }

        return $transformed;
    }

    /**
     * Transform attributes model.
     *
     * @return array
     */
    public function transformToFrontName(array $array = [])
    {
        $transformed = [];
        $transformation = $this->getTransformation();

        if (key_exists('public_id', $array)) {
            $transformed['id'] = $array['public_id'];
        }

        foreach ($transformation as $name => $new_name) {

            if (! key_exists($name, $array)) {

                foreach ($array as $field => $rule) {
                    if ($this->getOnlyField($field) == $name) {
                        $transformed[$field] = $array[$field];
                    }
                }
                continue;
            }

            $transformed[$new_name] = $array[$name];
        }

        return $transformed;
    }

    /**
     * Get the transformation data
     *
     * @return array
     */
    private function getTransformation()
    {
        $transforms = [];
        foreach ($this->fields as $field => $extra) {
            if (is_int($field)) {
                $transforms[$extra] = $extra;
                continue;
            }

            $transform = (key_exists('transform', $extra)) ? $extra['transform'] : $field;

            if ($transform === false) {
                continue;
            }

            $transforms[$field] = $transform;
        }

        return $transforms;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function getAutocomplete()
    {
        $autocomplete = [];
        foreach ($this->fields as $field => $extra) {
            if (! key_exists('autocomplete', $extra)) {
                continue;
            }

            $autocomplete[] = $field;
        }

        return $autocomplete;
    }

    /**
     * Validate store action
     *
     * @return array
     */
    public function store()
    {
        return $this->rules();
    }

    /**
     * Validate update action
     *
     * @return array
     */
    public function update()
    {
        return $this->rules();
    }

    /**
     * Validate update action
     *
     * @return array
     */
    public function associate()
    {
        return [];
    }

    /**
     * Validate update action
     *
     * @return array
     */
    public function autocomplete()
    {
        return [
            'text' => ''
        ];
    }

    public function simpleFilters() {
        return [];
    }
}
