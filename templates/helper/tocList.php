<?php

class tocListHelper
{
    /**
     * @var \Aura\Html\Helper\AnchorRaw
     */
    protected $anchorRaw;

    /**
     * @var int
     */
    protected $sublistLevelThrottle;

    /**
     * tocListHelper constructor.
     * @param \Aura\Html\Helper\AnchorRaw $anchorRaw
     */
    public function __construct(\Aura\Html\Helper\AnchorRaw $anchorRaw, \Bookdown\Bookdown\Config\RootConfig $config)
    {
        $this->anchorRaw = $anchorRaw;
        /**
         * Define on which level a collapsible sublist will be created.
         * @var integer
         */
        $this->sublistLevelThrottle = ($config->get('tocSublistLevelThrottle') !== null) ? $config->get('tocSublistLevelThrottle'): 2;
    }

    /**
     * @param array $entries
     * @return string
     */
    public function __invoke(array $entries)
    {
        return $this->renderTocList($entries);
    }

    /**
     * @param array $entries
     * @param null $collapseId
     * @return string
     */
    protected function renderTocList(array $entries, $collapseId = null)
    {
        $return = '';

        if (null !== $collapseId) {
            $listCssClass = 'class="list-toc collapse" id="' . $collapseId . '"';
        } else {
            $listCssClass = 'class="list-toc"';
        }

        $return .= '<ul ' . $listCssClass . '>';
        foreach ($entries as $entry) {
            if (isset($entry['children'])) {
                $return .= $this->renderTocListWithChildren($entry);

            } else {
                $return .= $this->renderTocListWithOutChildren($entry);
            }
        }
        $return .= '</ul>';

        return $return;
    }

    /**
     * @param $entry
     * @return string
     */
    protected function renderTocListWithChildren($entry)
    {
        $collapseId = null;

        $return = '<li class="list-group-item">';
        $return .= '<div class="row clearfix">';

        /** @var \Bookdown\Bookdown\Content\Heading $heading */
        foreach ($entry['headings'] as $heading) {

            $return .= $this->renderHeading($heading);
            if ($heading->getLevel() > $this->sublistLevelThrottle) {
                $collapseId = 'collapse-' . str_replace('.', '-', trim($heading->getNumber(), '.'));
                $return .= '<a class="bbt-toc-toggle badge glyphicon collapsed" href="#' . $collapseId . '" data-toggle="collapse" aria-controls="' . $collapseId . '" aria-expanded="false"></a>';
            }
        }
        $return .= '</div>';

        $return .= $this->renderTocList($entry['children'], $collapseId);
        $return .= '</li>';

        return $return;
    }

    /**
     * @param $entry
     * @return string
     */
    protected function renderTocListWithOutChildren($entry)
    {
        $return = '';

        /** @var \Bookdown\Bookdown\Content\Heading $heading */
        foreach ($entry['headings'] as $heading) {
            $return .= '<li class="list-group-item">';
            $return .= '<div class="row clearfix">';
            $return .= $this->renderHeading($heading);
            $return .= '</div>';
            $return .= '</li>';
        }

        return $return;
    }

    protected function renderHeading(\Bookdown\Bookdown\Content\Heading $heading)
    {
        $return = '<div class="col-sm-3">' . '<span class="text-number">' . $heading->getNumber() . '</span></div>';
        $return .= '<div class="col-sm-9">' . $this->anchorRaw->__invoke($heading->getHref(), $heading->getTitle()) . '</div>';

        return $return;
    }
}