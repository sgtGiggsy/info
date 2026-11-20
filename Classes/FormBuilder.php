<?php

namespace Infoinventory\Classes;

class FormBuilder
{
    private array $mezok = [];
    private string $elsomezo;
    private string $format;
    private string $submit;
    private string $formhead;
    private int $submitoszlop = 0;

    public function __construct()
    {
        $this->Hidden('csrf_token');
        $this->SetValue('csrf_token', $_SESSION['csrf_token']);
    }

    public function Text(string $name, string $label, bool $required = false): self {
        if ($required)
            $required = 'required';
        else
            $required = '';
        $this->mezok[] = [
            'type' => 'text',
            'name' => $name,
            'label' => $label,
            'value' => null,
            'required' => $required,
            'step' => ''
        ];
        return $this;
    }

    public function Email(string $name, string $label, bool $required = false): self {
        if ($required)
            $required = 'required';
        else
            $required = '';
        $this->mezok[] = [
            'type' => 'email',
            'name' => $name,
            'label' => $label,
            'value' => null,
            'required' => $required,
            'step' => ''
        ];
        return $this;
    }

    public function TextBox(string $name, string $label, bool $required = false): self {
        if ($required)
            $required = 'required';
        else
            $required = '';
        $this->mezok[] = [
            'type' => 'textbox',
            'name' => $name,
            'label' => $label,
            'value' => null,
            'required' => $required
        ];
        return $this;
    }

    public function Number(string $name, string $label, bool $float = false, bool $required = false): self {
        if ($required)
            $required = 'required';
        else
            $required = '';

        $step = "step='any'";
        if(!$float)
            $step = '';
        $this->mezok[] = [
            'type' => 'number',
            'name' => $name,
            'label' => $label,
            'value' => null,
            'required' => $required,
            'step' => $step
        ];
        return $this;
    }

    public function Hidden(string $name): self {
        $this->mezok[] = [
            'type' => 'hidden',
            'name' => $name,
            'value' => null,
            'required' => '',
            'step' => ''
        ];
        return $this;
    }

    public function Checkbox(string $name, string $label, bool $required = false): self {
        if ($required)
            $required = 'required';
        else
            $required = '';
        $this->mezok[] = [
            'type' => 'checkbox',
            'name' => $name,
            'value' => 1,
            'label' => $label,
            'checked' => false,
            'required' => $required
        ];
        return $this;
    }

    public function Select(string $name, string $label, array $options, bool $required = false): self {
        if ($required)
            $required = 'required';
        else
            $required = '';
        $this->mezok[] = [
            'type' => 'select',
            'name' => $name,
            'label' => $label,
            'options' => $options,
            'value' => null,
            'required' => $required
        ];
        return $this;
    }

    public function CustomInputField(string $name, string $inputhtml) : self {
        $this->mezok[] = [
            'type' => 'custominputfield',
            'name' => $name,
            'inputhtml' => $inputhtml
        ];
        return $this;
    }

    public function SetValue(string $name, mixed $value): self {
        foreach ($this->mezok as &$mezo) {
            if ($mezo['name'] === $name) {
                if ($mezo['type'] === 'checkbox') {
                    $mezo['checked'] = (bool)$value;
                } else {
                    $mezo['value'] = $value;
                }
            }
        }
        return $this;
    }

    public function SetHelperText(string $name, string $helperText): self {
        foreach ($this->mezok as &$mezo) {
            if ($mezo['name'] === $name) {
                $mezo['helperText'] = $helperText;
            }
        }
        return $this;
    }

    /**
     * Ezzel a metódussal lehet megadni, hogy egy FORM kétoszlopos formában renderelődjön-e
     * @param string|null $masodikoszlop_elsoelem Ez a megadott elem lesz a második oszlop első eleme. Nemlétező megadott elem esetén egyoszlopos lesz
     * @param string|null $format A kétoszlopos felosztás aránya. Alapértelmezetten 1-1, de lehet 2-1, 3-2, 3-1, 1-2, 1-3 és 2-3 is. Hibás, vagy null érték esetén 1-1-re áll automatikusan
     */
    public function SetTwoColForm(?string $masodikoszlop_elsoelem, ?string $format = '1-1') : void {
        if(!$masodikoszlop_elsoelem)
            return;

        foreach ($this->mezok as $mezo) {
            if($mezo['name'] === $masodikoszlop_elsoelem) {
                $this->elsomezo = $masodikoszlop_elsoelem;
                break;
            }
        }

        if(!isset($this->elsomezo))
            return;

        if(!in_array($format, ['1-1', '2-1', '3-2', '3-1', '1-2', '1-3', '2-3'])) {
            $format = '1-1';
        }
        $format = "double-" . $format;

        $this->format = $format;
    }

    /**
     * Ez a metódus hozzáadja a Submit elemet a FORM-hoz
     * @param string $buttontext A submit gombon megjelenő szöveg
     * @param int|null $submitoszlop A submit gomb helye a formban. Alapértelmezetten alul-középen jelenik meg. 1-es értékre állítva a baloldali, 2-esre a jobboldali oszlopban lesz renderelve
     */
    public function SetSubmit(string $buttontext, ?int $submitoszlop = 0) : void {
        $this->submit = '<div class="submit"><input type="submit" value="' . $buttontext . '"></div>';
        $this->submitoszlop = $submitoszlop;
    }

    public function SetFormHead(string $formaction) : void {
        $this->formhead = '<form action="' . $formaction. '" method="post">';
    }

    private function RenderFormHead() : string {
        if(isset($this->formhead))
            return $this->formhead;
        return '';
    }

    private function RenderFormFoot() : string {
        if(isset($this->formhead))
            return "</form>";
        return "";
    }

    private function RenderFormDiv() : string {
        if(isset($this->format))
            return '<div class="' . $this->format . '"><div>';
        return '';
    }

    private function RenderFormEnd() : string {
        $retstr = '';
        if(isset($this->format) && !($this->submitoszlop == 1 || $this->submitoszlop == 2))
            $retstr .= "</div>";

        if(isset($this->submit) && $this->submitoszlop !== 1) {
            $retstr .= $this->submit;
            if(isset($this->format) && $this->submitoszlop == 2)
                $retstr .= "</div>";
        }

        if(isset($this->format))
            $retstr .= '</div>';
        return $retstr;
    }

    private function RenderNextCol(string $mezonev) : string {
        $retstr = '';
        if(isset($this->format) && $mezonev == $this->elsomezo) {
            if($this->submitoszlop == 1)
                $retstr .= $this->submit;
            $retstr .= '</div><div>';
        }
        return $retstr;
    }

    public function Render(): string {
        $html = $this->RenderFormHead();
        $html .= $this->RenderFormDiv();

        foreach ($this->mezok as $mezo) {
            $html .= $this->RenderNextCol($mezo['name']);

            $checkbradio = $mezo['type'] == 'checkbox' || $mezo['type'] == 'radio';
            $hidden = $mezo['type'] == 'hidden';
            if(!$hidden) {
                $html .= '<div class="inputcont-' . $mezo['type'];
                $html .= ($checkbradio) ? '"><div class="customcbwrapper' : '';
                $html .= '">';
            }

            if(!$checkbradio && !$hidden)
                $html .= '<label for="' . $mezo['name'] . '">' . htmlspecialchars($mezo['label']) . '</label>';

            switch ($mezo['type']) {
                case 'email':
                case 'text':
                case 'number':
                case 'hidden':
                    $html .= sprintf(
                        '<input type="%s" name="%s" id="%s" value="%s" %s %s>',
                        $mezo['type'],
                        $mezo['name'],
                        $mezo['name'],
                        htmlspecialchars((string)$mezo['value']),
                        $mezo['step'],
                        $mezo['required']
                    );
                    break;
                case 'textbox':
                    $html .= sprintf(
                        '<textarea name="%s" id="%s" %s>%s</textarea>',
                        $mezo['name'],
                        $mezo['name'],
                        $mezo['required'],
                        htmlspecialchars((string)$mezo['value'])
                    );
                    break;

                case 'checkbox':
                    $html .= sprintf(
                        '<label class="customcb">
                                <input type="checkbox" name="%s" id="%s" value="%s" %s %s>',
                        $mezo['name'],
                                $mezo['name'],
                                $mezo['value'],
                                $mezo['checked'] ? 'checked' : '',
                                $mezo['required']
                    );
                    $html .= '<span class="customcbjelolo"></span></label>';
                    break;

                case 'select':
                    $html .= '<select name="'.$mezo['name'].'" id="' . $mezo['name'] . '" ' . $mezo['required'] . '>';
                    foreach ($mezo['options'] as $val => $text) {
                        $selected = ($val == $mezo['value']) ? 'selected' : '';
                        $html .= "<option value=\"$val\" $selected>$text</option>";
                    }
                    $html .= '</select>';
                    break;
                
                case 'custominputfield':
                    $html .= $mezo['inputhtml'];
                    break;
            }

            if($checkbradio && !$hidden)
                $html .= '<label for="' . $mezo['name'] . '">' . htmlspecialchars($mezo['label']) . '</label></div>';

            if(isset($mezo['helperText']) && !$hidden)
                $html .= '<small class="helpertext">' . $mezo['helperText'] . '</small>';

            if(!$hidden)
                $html .= '</div>';
        }
        $html .= $this->RenderFormEnd();
        $html .= $this->RenderFormFoot();

        return $html;
    }
}