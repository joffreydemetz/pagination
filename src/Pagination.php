<?php
/**
 * THIS SOFTWARE IS PRIVATE
 * CONTACT US FOR MORE INFORMATION
 * Joffrey Demetz <joffrey.demetz@gmail.com>
 * <http://callisto-framework.com>
 */
namespace JDZ\Pagination;

/**
 * Pagination
 *
 * @package Callisto
 * @author      Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Pagination 
{
  /**
   * Translatable strings
   * 
   * @var   [string]
   */
  protected $i18n = [
    'ALL' => 'All',
    'VIEW_ALL' => 'View all',
    'START' => 'Start',
    'END' => 'End',
    'PREVIOUS' => 'Previous',
    'NEXT' => 'Next',
  ];
  
  /**
   * Page is active
   * 
   * @var    bool
   */
  protected $active;
  
  /**
   * List offset
   * 
   * @var    int
   */
  protected $limitstart;
  
  /**
   * Maximum number of results to display
   * 
   * @var    int
   */
  protected $limit;
  
  /**
   * Total number of records
   * 
   * @var    int
   */
  protected $total;
  
  /**
   * Limit field prefix name ( [prefix]limit )
   * 
   * @var    string
   */
  protected $prefix;
  
  /**
   * Possible limit values
   * 
   * @var    []
   */
  protected $limits;
  
  /**
   * List offset
   * 
   * @var   Item[]
   */
  protected $pages;
  
  /**
   * Selected limit
   * 
   * @var   int
   */
  protected $selectedLimit;
  
  /**
   * The pages, prev, next links
   * 
   * @var   \stdClass|false
   */
  protected $links;
  
  /**
   * Show all the results
   * 
   * @var   bool
   */
  protected $viewall;
  
  /**
   * The pager instance
   * 
   * @var   Pagination
   */
  protected static $instance;
  
  /**
   * Get the pagination instance 
   *
   * @return   Pagination    The pagination instance
   */
  public static function getInstance(array $properties=[])
  {
    if ( !isset(self::$instance) ){
      self::$instance = new self($properties);
    }
    
    return self::$instance;
  }
  
  public function __construct(array $properties)
  {
    $this->active     = false;
    $this->viewall    = false;
    $this->limitstart = 0;
    $this->limit      = 0;
    $this->total      = 0;
    $this->prefix     = '';
    
    if ( isset($properties['i18n']) ){
      $this->i18n = array_merge($this->i18n, $properties['i18n']);
      unset($properties['i18n']);
    }
    
    foreach($properties as $key => $value){
      $this->{$key} = $value;
    }
    
    $this->total      = (int)$this->total;
    $this->limit      = (int)$this->limit;
    $this->limitstart = (int)$this->limitstart;
    
    $this->checkRange();
    
    $this->setPages();
    $this->setLimits();
    
    if ( $this->limit === 0 ){
      $this->viewall = true;
    }
    
    $this->active = true; //( $this->total >= $this->limit ); 
    $this->links  = $this->getItems(); 
  }
  
  public function toTemplate()
  {
    return [
      'active'        => $this->active,
      'limitstart'    => $this->limitstart,
      'limit'         => $this->limit,
      'total'         => $this->total,
      'prefix'        => $this->prefix,
      'limits'        => $this->limits,
      'pages'         => $this->pages,
      'selectedLimit' => $this->selectedLimit,
      'links'         => $this->links,
      'viewall'       => $this->viewall,
    ];
  }
  
  protected function checkRange()
  {
    if ( $this->viewall === true ){
      $this->limit      = 0;
      $this->limitstart = 0;
    }
    
    $this->selectedLimit = $this->limit;
    
    if ( $this->limit > $this->total ){
      $this->limitstart = 0;
    }
    
    if ( $this->limit === 0 ){
      $this->limit      = $this->total;
      $this->limitstart = 0;
    }
    
    /*
     * If limitstart is greater than total (i.e. we are asked to display records that don't exist)
     * then set limitstart to display the last natural page of results
     */
    if ( $this->limitstart > $this->total - $this->limit ){
      $this->limitstart = max(0, (int) (ceil($this->total / $this->limit) - 1) * $this->limit);
    }    
  }
  
  protected function setPages()
  {
    $total   = 0;
    $current = 1;
    
    if ( $this->limit > 0 ){
      $total   = ceil($this->total / $this->limit);
      $current = ceil(($this->limitstart + 1) / $this->limit);
    }
    
    // Set the pagination iteration loop values.
    $displayedPages = 10;
    
    $start = $current - ($displayedPages / 2);
    
    if ( $start < 1 ){
      $start = 1;
    }
    
    if ( ($start + $displayedPages) > $total ){
      $stop = $total;
      if ( $total < $displayedPages ){
        $start = 1;
      }
      else {
        $start = $total - $displayedPages + 1;
      }
    }
    else {
      $stop = ($start + $displayedPages - 1);
    }
    
    $this->pages = [
      'total'   => (int)$total,
      'current' => (int)$current,
      'start'   => (int)$start,
      'stop'    => (int)$stop,
    ];
  }
  
  protected function setLimits()
  {
    $limits=[];
    for($i=5; $i<=30; $i+=5){
      $limits[$i] = $i;
    }
    $limits[50]  = 50;
    $limits[100] = 100;
    $limits[0] = $this->i18n['ALL'];
    
    $this->limits = $limits;
  }
  
  /**
   * Return the pages
   * 
   * @return  \stdClass
   */
  protected function getItems()
  {
    // $links->all = new PaginationItem($this->i18n['VIEW_ALL']);
    // if ( !$this->viewall === true ){
      // $links->all->base = '0';
    // }
    if ( $this->pages['total'] === 1 ){
      return false;
    }
    
    $pages = [];
    
    for($i=$this->pages['start']; $i <= $this->pages['stop']; $i++){
      $offset = ($i - 1) * $this->limit;
      
      $pages[$i] = new PaginationItem($i);
      
      if ( $i == $this->pages['current'] ){
        $pages[$i]->current = true;
      }
      else {
        $pages[$i]->base = $offset;
      }
    }
    
    if ( empty($pages) ){
      return false;
    }
    
    $links = new \stdClass;
    $links->pages    = $pages;
    $links->start    = new PaginationItem($this->i18n['START']);
    $links->previous = new PaginationItem($this->i18n['PREVIOUS']);
    
    if ( $this->pages['current'] > 1 ){
      $page = ($this->pages['current'] - 2) * $this->limit;
      
      $links->start->base    = '0';
      $links->previous->base = $page;
    }
    else {
      $links->previous->disabled = true;
    }
    
    // Set the next and end data objects.
    $links->next = new PaginationItem($this->i18n['NEXT']);
    $links->end  = new PaginationItem($this->i18n['END']);
    
    if ( $this->pages['current'] < $this->pages['total'] ){
      $next = $this->pages['current'] * $this->limit;
      $end = ($this->pages['total'] - 1) * $this->limit;
      
      $links->next->base = $next;
    }
    else {
      $links->next->disabled = true;
    }
    
    return $links;
  }
}
