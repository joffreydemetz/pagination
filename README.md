# Pagination

Simple pagination object for my needs.
Inspired by the Joomla 2.5 Pagination Object

First instanciate

```
$pagination = Pagination::getInstance([
  'total' => 52, 
  'limitstart' => 0, 
  'limit' => 10,
]);

$vars = $pagination->toTemplate();
```

$vars is an array containing :
- active
- limitstart
- limit
- total
- prefix
- limits
- pages
- selectedLimit
- links
- viewall

Finally wrap it in a twig template that uses the bootstrap pagination module

{% spaceless %}
<div id="pagination-box">
 <form id="pagination" name="adminListPagination">
  {% if pagination.links %}
  <div class="pagination-pager">
   <ul class="pagination">
    {% if pagination.links.previous.disabled %}
    <li class="previous disabled"><a href="#" data-toggle="tooltip" title="{{ pagination.links.previous.text }}">&laquo;</a></li>
    {% else %}
    <li class="previous"><a href="#" data-toggle="tooltip" title="{{ pagination.links.previous.text }}">&laquo;</a></li>
    {% endif %}
    {% for page in pagination.links.pages %}
      {% if page.current %}
    <li class="active"><a href="#">{{ page.text }}</a></li>
      {% else %}
        {% if page.base %}<li><a href="#" title="{{ page.text }}" data-paginate-start="{{ page.base }}">{{ page.text }}</a></li>
        {% else %}<li><a href="#" title="{{ page.text }}" data-paginate-start="0">{{ page.text }}</a></li>
        {% endif %}
      {% endif %}
    {% endfor %} 
    {% if pagination.links.next.disabled %}
    <li class="next disabled"><a href="#" data-toggle="tooltip" title="{{ pagination.links.next.text }}">&raquo;</a></li>
    {% else %}
    <li class="next"><a href="#" data-toggle="tooltip" title="{{ pagination.links.next.text }}">&raquo;</a></li>
    {% endif %}
   </ul>
  </div>
  {% endif %}
  {% if pagination.limits %}
  <div class="pagination-limit">
   <span>Display</span>
   <select id="{{ pagination.prefix }}limit" name="{{ pagination.prefix }}limit" class="form-control" size="1">
    {% for limit, limitText in pagination.limits %}
    <option value="{{ limit }}"{% if limit == pagination.selectedLimit %} selected="selected"{% endif %}>{{ limitText }}</option>
    {% endfor %}
   </select>
   <span>results per page</span>
  </div>
  {% endif %}
  {% if pagination.links.pages.total > 1 %}
  <div class="pagination-counter">{{ 'Page %d on %d'|format(pagination.pages.current, pagination.pages.total) }}</div>
  {% endif %}
  <input type="hidden" name="limitstart" value="{{ pagination.limitstart }}" />
 </form>
</div>
{% endspaceless %} 
```

.. and add some style

```
<style>
#pagination-box {
    background: rgba(255,255,255,.78);
    border-top: 1px solid #F1F1F1;
    padding: 10px
}

#pagination-box .pagination-pager {
    width: 80%;
    margin: 0 auto 10px auto;
    text-align: center;
}

#pagination-box .pagination-pager>ul.pagination {
    margin: 0 auto
}

#pagination-box .pagination-limit {
    width: 50%;
    margin: 0 auto;
    text-align: center
}

#pagination-box .pagination-limit>span {
    width: 120px;
    display: inline-block
}

#pagination-box .pagination-limit>span:first-of-type {
    text-align: right;
    padding-right: 4px
}

#pagination-box .pagination-limit>span:last-of-type {
    text-align: left;
    padding-left: 4px
}

#pagination-box .pagination-limit>select {
    width: auto;
    height: 23px;
    padding: 2px 5px;
    display: inline-block
}

#pagination-box .pagination-counter {
    width: 80%;
    margin: 10px auto 0 auto;
    text-align: center
}

#pagination-box .pagination-limit + .pagination-counter {
    margin-top: 10px
}

@media(min-width: 768px) {
    #pagination-box {
        border-top-width:6px
    }
}
</style>
