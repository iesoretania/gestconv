<?php
/*
  GESTCONV - Aplicación web para la gestión de la convivencia en centros educativos

  Copyright (C) 2015: Luis Ramón López López

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU Affero General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU Affero General Public License for more details.

  You should have received a copy of the GNU Affero General Public License
  along with this program.  If not, see [http://www.gnu.org/licenses/].
*/

namespace AppBundle\Utils;

class InformePDF extends \TCPDF
{
    /**
     * Header image logos.
     * @protected
     */
    protected $header_logos = [];

    /**
     * Header contents
     * @protected
     */
    protected $header_texts = [];

    /**
     * Set header data.
     * @param $ln (array) header image logos
     * @param $texts (string) header image logo width in mm
     * @public
     */
    public function setExtendedHeaderData($ln=[], $texts=[])
    {
        $this->header_logos = $ln;
        $this->header_texts = $texts;
    }

    /**
     * Returns header data
     * @return array
     * @public
     */
    public function getExtendedHeaderData()
    {
        $ret = array();
        $ret['logos'] = $this->header_logos;
        $ret['header_texts'] = $this->header_texts;
        return $ret;
    }

    // Cabecera
    public function header()
    {
        $headerfont = $this->getHeaderFont();
        $headerdata = $this->getExtendedHeaderData();
        $this->y = $this->header_margin;
        if ($this->rtl) {
            $this->x = $this->w - $this->original_rMargin;
        } else {
            $this->x = $this->original_lMargin;
        }
        $logos = $headerdata['logos'];
        $captions = $headerdata['header_texts'];

        foreach ($logos as $i => $logo) {
            $logos[$i] = K_PATH_IMAGES.$logo;
        }

        $this->SetFont($headerfont[0], $headerfont[1], $headerfont[2]);
        $w_page = isset($this->l['w_page']) ? $this->l['w_page'].' ' : 'Pág. ';
        if (empty($this->pagegroups)) {
            $pagenumtxt = $w_page.$this->PageNo().' de '.$this->getAliasNbPages();
        } else {
            $pagenumtxt = $w_page.$this->getPageNumGroupAlias().' de '.$this->getPageGroupAlias();
        }

        $tbl = <<<EOD
<table cellspacing="0" cellpadding="1" border="0.75" align="center">
    <tr>
        <td rowspan="3" width="15%"></td>
        <td colspan="3" width="70%">{$captions[0]}</td>
        <td rowspan="3" width="15%"></td>
    </tr>
    <tr>
        <td>{$captions[1]}</td>
        <td colspan="2">{$captions[2]}</td>
    </tr>
    <tr>
       <td>{$captions[3]}</td>
       <td>{$captions[4]}</td>
       <td>{$pagenumtxt}</td>
    </tr>
</table>
EOD;
        $this->writeHTML($tbl, true, false, false, false, '');
        $this->x = $this->original_lMargin + 1;
        $this->y = $this->header_margin + 4;
        $this->Image($logos[0], '', '', 10);
        $this->x = $this->original_lMargin + 15;
        $this->Image($logos[1], '', '', 10);
        $this->x = $this->w - $this->rMargin - 23;
        $this->y = $this->header_margin + 0.5;
        $this->Image($logos[2], '', '', 20, 0, '', '', '', '', 300, '', false, false, 0, '');

    }

    public function footer()
    {
        $cur_y = $this->y;
        $this->SetTextColorArray($this->footer_text_color);
        //set style for cell border
        $line_width = (0.85 / $this->k);
        $this->SetLineStyle(array('width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $this->footer_line_color));
        //print document barcode
        $barcode = $this->getBarcode();
        if (!empty($barcode)) {
            $this->Ln($line_width);
            $style = array(
                'position' => $this->rtl?'R':'L',
                'align' => $this->rtl?'R':'L',
                'stretch' => false,
                'fitwidth' => true,
                'cellfitalign' => '',
                'border' => false,
                'padding' => 0,
                'fgcolor' => array(0,0,0),
                'bgcolor' => false,
                'text' => false
            );
            $this->write1DBarcode($barcode, 'C128', '', $cur_y + $line_width, '', (($this->footer_margin / 3) - $line_width), 0.3, $style, '');
        }

        $headerdata = $this->getExtendedHeaderData();
        $captions = $headerdata['header_texts'];
        $pagenumtxt = $captions[3] . ' ' . $captions[4];

        $this->SetY($cur_y);
        //Print page number
        if ($this->getRTL()) {
            $this->SetX($this->original_rMargin);
            $this->Cell(0, 0, $pagenumtxt, 'T', 0, 'L');
        } else {
            $this->SetX($this->original_lMargin);
            $this->Cell(0, 0, $pagenumtxt, 'T', 0, 'R');
        }
    }
}
