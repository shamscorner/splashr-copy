<?php

namespace App\View\Components;

use Illuminate\View\Component;

class InputTextField extends Component
{
    /**
     * the input type
     * 
     * @var string
     */
    public $type;

    /**
     * id of the input
     * 
     * @var string
     */
    public $id;

    /**
     * input label
     * 
     * @var string
     */
    public $label;

    /**
     * input name
     * 
     * @var string
     */
    public $name;

    /**
     * input value
     * 
     * @var string
     */
    public $value;

    /**
     * input is required or not
     * 
     * @var bool
     */
    public $required;

    /**
     * input is autofocused or not
     * 
     * @var bool
     */
    public $autofocus;

    /**
     * input autocomplete
     * 
     * @var string
     */
    public $autocomplete;

    /**
     * Create a new component instance.
     *
     * @param string $type
     * @param string $id
     * @param string $label
     * @param string $name
     * @param string $value
     * @param string $required
     * @param string $autofocus
     * @param string $autocomplete
     * 
     * @return void
     */
    public function __construct($type, $id, $label, $name, $value = "", $required = false, $autofocus =  false, $autocomplete = '')
    {
        $this->type = $type;
        $this->id = $id;
        $this->label = $label;
        $this->name = $name;
        $this->value = $value;
        $this->required = $required;
        $this->autofocus = $autofocus;
        $this->autocomplete = $autocomplete;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.input-text-field');
    }
}
