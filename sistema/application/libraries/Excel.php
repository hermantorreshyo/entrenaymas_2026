<?php
class Excel {
    
  private function letter($i) {
    $alphabet = ['A','B','C','D','E','F','G','H','I','J','K',
                 'L','M','N','O','P','Q','R','S','T','U',
                 'V','W','X','Y','Z'];
    $entera = floor($i / 26);
    $modulo = $i % 26;
    if ($entera == 0) {
      return $alphabet[$modulo];
    } else if ($entera > 0) {
      return $alphabet[$entera-1].$alphabet[$modulo];
    }
  }

  public function create($conf) {
    
    $title = $conf["title"];
    $date = $conf["date"];
    $header = $conf["header"];
    $datos = isset($conf["data"]) ? $conf["data"] : array();
    $datos2 = isset($conf["datos"]) ? $conf["datos"] : array();
    $footer = $conf["footer"];
    $filename = $conf["filename"];
    
    $fila = 1;
    include("resources/php/Excel/PHPExcel.php");
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->setActiveSheetIndex(0);
    if (!empty($title)) {
      $objPHPExcel->getActiveSheet()->SetCellValue('A'.$fila, $title);
      $objPHPExcel->getActiveSheet()->SetCellValue('D'.$fila, $date);
      $fila++;
      $fila++;
    }
    
    // Encabezado
    if (sizeof($header)>0) {
      for($j=0;$j<sizeof($header);$j++) {
        $objPHPExcel->getActiveSheet()->SetCellValue(($this->letter($j)).$fila,$header[$j]);
      }
      $fila++;
    }
    
    // Datos
    foreach($datos as $obj) {
      $j=0;
      foreach($obj as $key => $value) {
        $objPHPExcel->getActiveSheet()->SetCellValue(($this->letter($j)).$fila,$value);
        $j++;
      }
      $fila++;
    }

    if (sizeof($datos2)>0) {
      for($j=0;$j<sizeof($datos2);$j++) {
        $obj = $datos2[$j];
        for($k=0;$k<sizeof($obj);$k++) {
          $objPHPExcel->getActiveSheet()->SetCellValue(($this->letter($k)).$fila,$obj[$k]);
        }
        $fila++;
      }
    }    
    
    // Footer
    if (sizeof($footer)>0) {
      for($j=0;$j<sizeof($footer);$j++) {
        $objPHPExcel->getActiveSheet()->SetCellValue(($this->letter($j)).$fila,$footer[$j]);
      }
      $fila++;
    }        
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
    header('Cache-Control: max-age=0');
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    $objWriter->save('php://output');        
  }
    
}
?>