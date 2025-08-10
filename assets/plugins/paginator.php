<?php
/**
*@author  				The-Di-Lab
*@email   				thedilab@gmail.com
*@website 				www.the-di-lab.com
*@version               1.0
**/
class Paginator {
		public $itemsPerPage;
		public $range;
		public $currentPage;
		public $total;
		public $textNav;
		private $_navigation;
		private $_link;
		private $_pageNumHtml;
		private $_itemHtml;
		private $start;
		private $end;
        /**
         * Constructor
         */
        public function __construct()
        {
        	//set default values
        	$this->itemsPerPage = 20;
					$this->range        = 1;
					$this->currentPage  = 1;
					$this->total		= 0;
					$this->start		= 0;
					$this->end		= 0;
					$this->textNav 		= true;
					$this->itemSelect   = array(20,50,100,300);
					//private values
					$this->_navigation  = array(
							'next'=>'Next',
							'pre' =>'Prev',
							'fir' =>'First',
							'las' =>'Last',
							'ipp' =>'Showing',
							'ipp2' =>'data per page'
					);
        	$this->_link 		 = filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING);
        	$this->_pageNumHtml  = '';
        	$this->_itemHtml 	 = '';
        }

        /**
         * paginate main function
         *
         * @author              The-Di-Lab <thedilab@gmail.com>
         * @access              public
         * @return              type
         */
		public function paginate()
		{
			//get current page
			if(isset($_GET['current'])){
				$this->currentPage  = $_GET['current'];
			}
			//get item per page
			if(isset($_GET['item'])){
				$this->itemsPerPage = $_GET['item'];

			}
			//get page numbers
			if ($this->total >0) {
				// code...
				$this->_pageNumHtml = $this->_getPageNumbers();
				//get item per page select box
				$this->_itemHtml	= $this->_getItemSelect();
			}
		}
    public function indexCounter()
    {
      return ($this->currentPage - 1) * $this->itemsPerPage;
    }

        /**
         * return pagination numbers in a format of UL list
         *
         * @author              The-Di-Lab <thedilab@gmail.com>
         * @access              public
         * @param               type $parameter
         * @return              string
         */
        public function pageNumbers()
        {
        	if(empty($this->_pageNumHtml)){
        		exit('Please call function paginate() first.');
        	}
        	return $this->_pageNumHtml;
        }

        /**
         * return jump menu in a format of select box
         *
         * @author              The-Di-Lab <thedilab@gmail.com>
         * @access              public
         * @return              string
         */
        public function itemsPerPage()
        {
        	if(empty($this->_itemHtml)){
        		exit('Please call function paginate() first.');
        	}
        	return $this->_itemHtml;
        }

       	/**
         * return page numbers html formats
         *
         * @author              The-Di-Lab <thedilab@gmail.com>
         * @access              public
         * @return              string
         */
        private function  _getPageNumbersX()
        {
        	$html  = ' <nav ><ul class="pagination pagination-circled justify-content-center ">';

        	//previous link button
					if($this->textNav&&($this->currentPage > 1)){
						$html  .= '<li class="page-item" ><a class="page-link first" href="'.$this->_link .'?current=1&item='.$this->itemsPerPage.'"';
						$html  .= '><i class="fa fa-angle-double-left"></i></a></li>';
						$html  .= '<li class="page-item"><a class="page-link " href="'.$this->_link .'?current='.($this->currentPage-1).'&item='.$this->itemsPerPage.'"';
						$html  .= '><i class="fa fa-angle-left"></i></a></li>';
					}
        	//do ranged pagination only when total pages is greater than the range
        	if($this->total > $this->range){
						$this->start = ($this->currentPage <= $this->range)?1:($this->currentPage - $this->range);
						$this->end   = ($this->total - $this->currentPage >= $this->range)?($this->currentPage+$this->range): $this->total;
        	}else{
        		// $this->start = $this->_navigation['fir'];
						$this->start = is_numeric($this->_navigation['fir']) ? $this->_navigation['fir'] : 1;

						$this->end = $this->total;
        	}
        	//loop through page numbers
        	for($i = $this->start; $i <= $this->end; $i++){
						$html  .= '<li  class="page-item';
	        	if($i==$this->currentPage) $html  .= " active";
	          $html  .= '"><a class="page-link" href="'.$this->_link .'?current='.$i.'&item='.$this->itemsPerPage.'"';

						$html  .= '>'.$i.'</a></li>';
					}
        	//next link button
		    	if($this->textNav&&($this->currentPage<$this->total)&&$this->itemsPerPage!='All'){
						$html  .= '<li class="page-item"><a class="page-link " href="'.$this->_link .'?current='.($this->currentPage+1).'&item='.$this->itemsPerPage.'"';
						$html  .= '><i class="fa fa-angle-right"></i></a></li>';
					}
					if($this->itemsPerPage!='All'){
						$current = $this->total > 0 ? $this->total / $this->itemsPerPage : 0;
						$html  .= '<li class="page-item"><a class="page-link last" href="'.$this->_link .'?current='.ceil($current ).'&item='.$this->itemsPerPage.'"';
						$html  .= '><i class="fa fa-angle-double-right"></i></a></li>';
					}
	        	$html .= '</ul></nav>';
	        	return $html;
        }
				private function _getPageNumbers()
				{
				  // fallback link if not set
				  $this->_link = $this->_link ?? basename($_SERVER['PHP_SELF']);

				  $html  = '<nav><ul class="pagination pagination-circled justify-content-center">';

				  // Previous navigation
				  if ($this->textNav && ($this->currentPage > 1)) {
				      $html .= '<li class="page-item"><a class="page-link first" href="' . $this->_link . '?' . $this->_buildQuery([
				          'current' => 1,
				          'item' => $this->itemsPerPage
				      ]) . '"><i class="fa fa-angle-double-left"></i></a></li>';

				      $html .= '<li class="page-item"><a class="page-link" href="' . $this->_link . '?' . $this->_buildQuery([
				          'current' => $this->currentPage - 1,
				          'item' => $this->itemsPerPage
				      ]) . '"><i class="fa fa-angle-left"></i></a></li>';
				  }

				  // Pagination range calculation
				  if ($this->total > $this->range) {
				      $this->start = ($this->currentPage <= $this->range) ? 1 : ($this->currentPage - $this->range);
				      $this->end = ($this->total - $this->currentPage >= $this->range) ? ($this->currentPage + $this->range) : $this->total;
				  } else {
				      // $this->start = $this->_navigation['fir'];
							$this->start = is_numeric($this->_navigation['fir']) ? $this->_navigation['fir'] : 1;
							
				      $this->end = $this->total;
				  }

				  // Page number loop
				  for ($i = $this->start; $i <= $this->end; $i++) {
				      $html .= '<li class="page-item' . ($i == $this->currentPage ? ' active' : '') . '">';
				      $html .= '<a class="page-link" href="' . $this->_link . '?' . $this->_buildQuery([
				          'current' => $i,
				          'item' => $this->itemsPerPage
				      ]) . '">' . $i . '</a></li>';
				  }

				  // Next navigation
				  if ($this->textNav && ($this->currentPage < $this->total) && $this->itemsPerPage !== 'All') {
				      $html .= '<li class="page-item"><a class="page-link" href="' . $this->_link . '?' . $this->_buildQuery([
				          'current' => $this->currentPage + 1,
				          'item' => $this->itemsPerPage
				      ]) . '"><i class="fa fa-angle-right"></i></a></li>';
				  }

				  // Last page navigation
				  if ($this->itemsPerPage !== 'All') {
				      $lastPage = $this->total > 0 ? ceil($this->total / $this->itemsPerPage) : 1;
				      $html .= '<li class="page-item"><a class="page-link last" href="' . $this->_link . '?' . $this->_buildQuery([
				          'current' => $lastPage,
				          'item' => $this->itemsPerPage
				      ]) . '"><i class="fa fa-angle-double-right"></i></a></li>';
				  }

				  $html .= '</ul></nav>';
				  return $html;
				}
				private function _buildQuery($extraParams = [])
				{
				    // Start from current query string
				    $params = $_GET;

				    // Remove pagination keys to avoid duplication
				    unset($params['current'], $params['item']);

				    // Merge with new ones
				    $params = array_merge($params, $extraParams);

				    return http_build_query($params);
				}

        /**
         * return item select box
         *
         * @author              The-Di-Lab <thedilab@gmail.com>
         * @access              public
         * @return              string
         */
        private function  _getItemSelect()
        {
        	$items = '';
	   			$ippArray = $this->itemSelect;
	   			foreach($ippArray as $ippOpt){
			    	$active = ($ippOpt == $this->itemsPerPage) ? 'active':'';
			    	$items .= '<a class="dropdown-item '.$active.'" href="'.$this->_link.'?current=1&item='.$ippOpt.'">'.$ippOpt.'</a>';
	   			}
		    	// return "<span class=\"paginate\">".$this->_navigation['ipp']."</span>
		    	// <select class=\"paginate\" onchange=\"window.location='$this->_link?current=1&item='+this[this.selectedIndex].value;return false\">
					// $items
					// </select>
	        // <span class=\"paginate\">".$this->_navigation['ipp2']."</span>\n";
					$output = '<div class="float-md-right"><span class="text-muted text-small">';
					$output .= '<button class="btn btn-outline-secondary dropdown-toggle p-2 me-2 " style="border-radius: 5px" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.$this->itemsPerPage.'</button>';
					$output .= '<div class="dropdown-menu dropdown-menu-right">';
					$output .= $items;
					$output .= '</div>';
					$output .= 'Displaying '.$this->start.'- '.$this->end.' of '.$this->total.' items </span>';
					$output .= '</div>';
					return $output;
        }
}
