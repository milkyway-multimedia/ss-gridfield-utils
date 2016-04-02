<?php namespace Milkyway\SS\GridFieldUtils\Common;

use Milkyway\SS\GridFieldUtils\Utilities;
use Exception;

trait HasModal
{
    public $dimensions = [
        'width'  => 0.8,
        'height' => 0.8,
    ];

    protected $urlSegment = 'modal';

    public function getURLHandlers($gridField)
    {
        return [
            $this->urlSegment . '/$ID' => 'handleItem',
        ];
    }

    public function htmlFragments($gridField)
    {
        $gridField->setAttribute('data-modal-dimensions', json_encode($this->dimensions));
        Utilities::include_requirements();
    }

    public function handleItem($gridField, $request)
    {
        $detailForm = $gridField->Config->getComponentByType('GridFieldDetailForm');

        if (!$detailForm) {
            throw new Exception('A GridFieldDetailForm is required to use a modal via this component');
        }

        Utilities::include_requirements();

        $detailForm->setTemplate('GridField_Modal_View');

        $itemEditCallback = $detailForm->getItemEditFormCallback();
        $link = $this->Link($gridField);

        $detailForm->setItemEditFormCallback(function ($form, $controller) use ($itemEditCallback, $link) {
            $form->setAttribute('data-modal-link', $link);
            $nonModalLink = explode('/', $controller->Link());
            array_pop($nonModalLink);
            $nonModalLink = implode('/', $nonModalLink);
            $form->setAttribute('data-non-modal-link', $nonModalLink);

            if ($itemEditCallback) {
                $itemEditCallback($form, $controller);
            }
        });

        return $detailForm->handleItem($gridField, $request);
    }

    protected function Link($gridField)
    {
        return $gridField->Link($this->urlSegment);
    }
}
