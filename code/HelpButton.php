<?php namespace Milkyway\SS\GridFieldUtils;
/**
 * Milkyway Multimedia
 * GridFieldHelpButton.php
 *
 * @package relatewell.org.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

class HelpButton implements
    \GridField_HTMLProvider,
    \GridField_URLHandler {
    private static $allowed_actions = array(
        'handleView',
    );

    protected $id;

    protected $title;

    protected $content;

    protected $template = 'GridFieldHelpButton_View';

    public function __construct($fragment = 'buttons-before-right', $title = '', $id = '') {
        $this->fragment = $fragment;
        $this->title    = $title ?: _t('GridFieldUtils.HELP', 'Help');
        $this->id = $id;
    }

    public function id() {
        return $this->id;
    }

    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    public function getContent() {
        return $this->content;
    }

    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTemplate($template) {
        $this->template = $template;
        return $this;
    }

    public function getTemplate() {
        return $this->template;
    }

    public function getHTMLFragments($grid) {
        Utilities::include_requirements();

        return array(
            $this->fragment => \ArrayData::create(array(
                    'Title' => $this->getTitle(),
                    'Link'  => $grid->Link('help-' . $this->makeSureIdIsUnique($grid)),
                )
            )->renderWith('GridFieldHelpButton'),
        );
    }

    public function getURLHandlers($grid) {
        return array(
            'help-' . $this->makeSureIdIsUnique($grid) => 'handleView',
        );
    }

    public function handleView($grid, $request) {
        if($grid->Form && $grid->Form->Record)
            $record = $grid->Form->Record;
        else
            $record = \ArrayData::create();

        $template = is_array($this->template) ? $this->template : [$this->template];
        array_push($template, 'GridFieldHelpButton_View');

        return $record->customise(array('Content' => $this->content))->renderWith($template);
    }

    protected function makeSureIdIsUnique($grid) {
        if(!$this->id)
            $this->id = 'default';

        $helpButtons = $grid->Config->getComponentsByType(__CLASS__);
        $ids = [];

        foreach($helpButtons as $helpButton) {
            if($helpButton !== $this)
                $ids[] = $helpButton->id();
        }

        $count = 2;

        while(in_array($this->id, $ids)) {
            $this->id = trim($this->id, '-') . '-' . $count;
            $count++;
        }

        return $this->id();
    }
} 