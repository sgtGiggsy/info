<?php

namespace Infoinventory\Classes;

class FormBuilder
{
    private array $mezok = [];
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

    public function SetValue(string $name, $value): self {
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

    public function Render(): string {
        $html = '';

        foreach ($this->mezok as $mezo) {
            $checkbradio = $mezo['type'] == 'checkbox' || $mezo['type'] == 'radio';
            $hidden = $mezo['type'] == 'hidden';
            if(!$hidden) {
                $html .= '<div class="inputcont-' . $mezo['type'];
                $html .= ($checkbradio) ? ' customcbwrapper' : '';
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
            }

            if($checkbradio && !$hidden)
                $html .= '<label for="' . $mezo['name'] . '">' . htmlspecialchars($mezo['label']) . '</label>';

            if(!$hidden)
                $html .= '</div>';
        }

        return $html;
    }
}