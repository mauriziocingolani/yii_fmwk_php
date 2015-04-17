<?php

/**
 * Modifica il comportamento di CLinkPager rendendo visibili i link
 * alla prima e all'ultima pagina (a meno che non si stia sulla prima
 * o sull'ultima pagina).
 * Le classi firstPageCssClass e lastPageCssClass (che assegnano di default 'diaply: hidden' ai due link)
 * vengono sostituiti con previousPageCssClass e nextPageCssClass rispettivamente.
 * 
 * @author Maurizio Cingolani <mauriziocingolani74@gmail.com>
 * @version 1.0
 */
class Pager extends CLinkPager {

    protected function createPageButtons() {
        if (($pageCount = $this->getPageCount()) <= 1)
            return array();

        list($beginPage, $endPage) = $this->getPageRange();
        $currentPage = $this->getCurrentPage(false); // currentPage is calculated in getPageRange()
        $buttons = array();

        // first page
        if ($this->firstPageLabel !== false && $currentPage > 0) :
            $buttons[] = $this->createPageButton($this->firstPageLabel, 0, $this->previousPageCssClass, $currentPage <= 0, false);
        endif;

        // prev page
        if ($this->prevPageLabel !== false && $currentPage > 0) :
            if (($page = $currentPage - 1) < 0)
                $page = 0;
            $buttons[] = $this->createPageButton($this->prevPageLabel, $page, $this->previousPageCssClass, $currentPage <= 0, false);
        endif;

        // internal pages
        for ($i = $beginPage; $i <= $endPage; ++$i) :
            $buttons[] = $this->createPageButton($i + 1, $i, $this->internalPageCssClass, false, $i == $currentPage);
        endfor;

        // next page
        if ($this->nextPageLabel !== false && $currentPage + 1 < $pageCount) :
            if (($page = $currentPage + 1) >= $pageCount - 1)
                $page = $pageCount - 1;
            $buttons[] = $this->createPageButton($this->nextPageLabel, $page, $this->nextPageCssClass, $currentPage >= $pageCount - 1, false);
        endif;

        // last page
        if ($this->lastPageLabel !== false && $currentPage + 1 < $pageCount) :
            $buttons[] = $this->createPageButton($this->lastPageLabel, $pageCount - 1, $this->nextPageCssClass, $currentPage >= $pageCount - 1, false);
        endif;

        return $buttons;
    }

}
