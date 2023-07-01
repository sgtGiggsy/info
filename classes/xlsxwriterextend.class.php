<?php

class XLSXWriter_Ext extends XLSXWriter
{
    /*public function writeCellManually($sheet_name, $row_number, $column_number, $value, $cell_style_idx)
	{
		if (empty($sheet_name))
			return;

		$this->initializeSheet($sheet_name);
		$sheet = &$this->sheets[$sheet_name];
        
        $file = new XLSXWriter_BuffererWriter($sheet);
        
        $cell_name = self::xlsCell($row_number, $column_number);

		if (!is_scalar($value) || $value==='') { //objects, array, empty
			$file->write('<c r="'.$cell_name.'" s="'.$cell_style_idx.'"/>');
		} elseif (is_string($value) && $value[0]=='='){
			$file->write('<c r="'.$cell_name.'" s="'.$cell_style_idx.'" t="s"><f>'.self::xmlspecialchars(ltrim($value, '=')).'</f></c>');
		}
	}*/

    private function initializeColumnTypes($header_types)
	{
		$column_types = array();
		foreach($header_types as $v)
		{
			$number_format = self::numberFormatStandardized($v);
			$number_format_type = self::determineNumberFormatType($number_format);
			$cell_style_idx = $this->addCellStyle($number_format, $style_string=null);
			$column_types[] = array('number_format' => $number_format,//contains excel format like 'YYYY-MM-DD HH:MM:SS'
									'number_format_type' => $number_format_type, //contains friendly format like 'datetime'
									'default_cell_style' => $cell_style_idx,
									);
		}
		return $column_types;
	}

    public function writeSheetRow_Custom($sheet_name, array $row, $row_options=null, array $cellids, $customstyle, $thirdstcellids = array(), $thirdstyle = null)
	{
		if (empty($sheet_name))
			return;

		$this->initializeSheet($sheet_name);
		$sheet = &$this->sheets[$sheet_name];
		if (count($sheet->columns) < count($row)) {
			$default_column_types = $this->initializeColumnTypes( array_fill($from=0, $until=count($row), 'GENERAL') );//will map to n_auto
			$sheet->columns = array_merge((array)$sheet->columns, $default_column_types);
		}
		
		if (!empty($row_options))
		{
			$ht = isset($row_options['height']) ? floatval($row_options['height']) : 12.1;
			$customHt = isset($row_options['height']) ? true : false;
			$hidden = isset($row_options['hidden']) ? (bool)($row_options['hidden']) : false;
			$collapsed = isset($row_options['collapsed']) ? (bool)($row_options['collapsed']) : false;
			$sheet->file_writer->write('<row collapsed="'.($collapsed ? 'true' : 'false').'" customFormat="false" customHeight="'.($customHt ? 'true' : 'false').'" hidden="'.($hidden ? 'true' : 'false').'" ht="'.($ht).'" outlineLevel="0" r="' . ($sheet->row_count + 1) . '">');
		}
		else
		{
			$sheet->file_writer->write('<row collapsed="false" customFormat="false" customHeight="false" hidden="false" ht="12.1" outlineLevel="0" r="' . ($sheet->row_count + 1) . '">');
		}

		$style = &$row_options;
        $style2  = &$customstyle;
		$style3 = &$thirdstyle;

		$c=0;
		foreach ($row as $value) {
            $number_format = $sheet->columns[$c]['number_format'];
			$number_format_type = $sheet->columns[$c]['number_format_type'];
			if(in_array($c, $cellids))
			{
				$cell_style_idx = empty($style2) ? $sheet->columns[$c]['default_cell_style'] : $this->addCellStyle( $number_format, json_encode(isset($style2[0]) ? $style2[$c] : $style2) );
				$this->writeCell($sheet->file_writer, $sheet->row_count, $c, $value, $number_format_type, $cell_style_idx);
			}
			elseif(in_array($c, $thirdstcellids))
			{
				$cell_style_idx = empty($style3) ? $sheet->columns[$c]['default_cell_style'] : $this->addCellStyle( $number_format, json_encode(isset($style3[0]) ? $style3[$c] : $style3) );
				$this->writeCell($sheet->file_writer, $sheet->row_count, $c, $value, $number_format_type, $cell_style_idx);
			}
			else
			{
				$cell_style_idx = empty($style) ? $sheet->columns[$c]['default_cell_style'] : $this->addCellStyle( $number_format, json_encode(isset($style[0]) ? $style[$c] : $style) );
				$this->writeCell($sheet->file_writer, $sheet->row_count, $c, $value, $number_format_type, $cell_style_idx);
			}
			$c++;
		}
		$sheet->file_writer->write('</row>');
		$sheet->row_count++;
		$this->current_sheet = $sheet_name;
	}

    //------------------------------------------------------------------
	private static function determineNumberFormatType($num_format)
	{
		$num_format = preg_replace("/\[(Black|Blue|Cyan|Green|Magenta|Red|White|Yellow)\]/i", "", $num_format);
		if ($num_format=='GENERAL') return 'n_auto';
		if ($num_format=='@') return 'n_string';
		if ($num_format=='0') return 'n_numeric';
		if (preg_match('/[H]{1,2}:[M]{1,2}(?![^"]*+")/i', $num_format)) return 'n_datetime';
		if (preg_match('/[M]{1,2}:[S]{1,2}(?![^"]*+")/i', $num_format)) return 'n_datetime';
		if (preg_match('/[Y]{2,4}(?![^"]*+")/i', $num_format)) return 'n_date';
		if (preg_match('/[D]{1,2}(?![^"]*+")/i', $num_format)) return 'n_date';
		if (preg_match('/[M]{1,2}(?![^"]*+")/i', $num_format)) return 'n_date';
		if (preg_match('/$(?![^"]*+")/', $num_format)) return 'n_numeric';
		if (preg_match('/%(?![^"]*+")/', $num_format)) return 'n_numeric';
		if (preg_match('/0(?![^"]*+")/', $num_format)) return 'n_numeric';
		return 'n_auto';
	}
	//------------------------------------------------------------------
	private static function numberFormatStandardized($num_format)
	{
		if ($num_format=='money') { $num_format='dollar'; }
		if ($num_format=='number') { $num_format='integer'; }

		if      ($num_format=='string')   $num_format='@';
		else if ($num_format=='integer')  $num_format='0';
		else if ($num_format=='date')     $num_format='YYYY-MM-DD';
		else if ($num_format=='datetime') $num_format='YYYY-MM-DD HH:MM:SS';
        else if ($num_format=='time')     $num_format='HH:MM:SS';
		else if ($num_format=='price')    $num_format='#,##0.00';
		else if ($num_format=='dollar')   $num_format='[$$-1009]#,##0.00;[RED]-[$$-1009]#,##0.00';
		else if ($num_format=='euro')     $num_format='#,##0.00 [$€-407];[RED]-#,##0.00 [$€-407]';
		$ignore_until='';
		$escaped = '';
		for($i=0,$ix=strlen($num_format); $i<$ix; $i++)
		{
			$c = $num_format[$i];
			if ($ignore_until=='' && $c=='[')
				$ignore_until=']';
			else if ($ignore_until=='' && $c=='"')
				$ignore_until='"';
			else if ($ignore_until==$c)
				$ignore_until='';
			if ($ignore_until=='' && ($c==' ' || $c=='-'  || $c=='('  || $c==')') && ($i==0 || $num_format[$i-1]!='_'))
				$escaped.= "\\".$c;
			else
				$escaped.= $c;
		}
		return $escaped;
	}

    private function addCellStyle($number_format, $cell_style_string)
	{
		$number_format_idx = self::add_to_list_get_index($this->number_formats, $number_format);
		$lookup_string = $number_format_idx.";".$cell_style_string;
		$cell_style_idx = self::add_to_list_get_index($this->cell_styles, $lookup_string);
		return $cell_style_idx;
	}

}