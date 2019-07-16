<?php

namespace app\components;
/**
 * Компонент для простановки fields и expand.
 * */
class DefaultSerializer extends \yii\rest\Serializer {

    public $defaultFields;
    public $defaultExpand;

    public function init() {
        parent::init();
        $this->defaultFields = !is_null($this->defaultFields) ? implode(",", $this->defaultFields) : $this->defaultFields;
        $this->defaultExpand = !is_null($this->defaultExpand) ? implode(",", $this->defaultExpand) : $this->defaultExpand;
    }

    protected function getRequestedFields() {
        $fields = is_null($this->request->get($this->fieldsParam)) ? $this->defaultFields : $this->request->get($this->fieldsParam);
        $expand = is_null($this->request->get($this->expandParam)) ? $this->defaultExpand : $this->request->get($this->expandParam);

        return [
            preg_split('/\s*,\s*/', $fields, -1, PREG_SPLIT_NO_EMPTY),
            preg_split('/\s*,\s*/', $expand, -1, PREG_SPLIT_NO_EMPTY),
        ];
    }

}