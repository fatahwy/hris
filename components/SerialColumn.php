<?php

namespace app\components;

class SerialColumn extends \kartik\grid\SerialColumn {

    public $dropButtons;

    public function init() {
        $this->initColumnSettings([
            // 'hiddenFromExport' => true,
            'mergeHeader' => false,
        ]);
    
    }


}
