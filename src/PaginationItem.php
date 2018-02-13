<?php 
/**
 * THIS SOFTWARE IS PRIVATE
 * CONTACT US FOR MORE INFORMATION
 * Joffrey Demetz <joffrey.demetz@gmail.com>
 * <http://callisto.izisawebsite.com>
 */
namespace JDZ\Pagination;

/**
 * Pagination item
 * 
 * @package Callisto
 * @author      Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class PaginationItem 
{
  /**
   * The link text.
   * 
   * @var    string
   */
  public $text;

  /**
   * The number of rows as a base offset.
   * 
   * @var    int
   */
  public $base;

  /**
   * Is link active
   * 
   * @var    bool
   */
  public $current;

  /**
   * Link is disabled
   * 
   * @var    bool
   */
  public $disabled;

  /**
   * Constructor
   *
   * @param   string  $text     The link text
   * @param   int     $base     The number of rows as a base offset
   * @param   bool    $current  Is the current page
   */
  public function __construct($text, $base=null, $current=false)
  {
    $this->text     = $text;
    $this->base     = $base;
    $this->current  = $current;
    $this->disabled = false;
  }
}
