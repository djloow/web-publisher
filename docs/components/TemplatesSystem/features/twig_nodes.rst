Custom Twig tags
------------------------------

Gimme allows you to fetch the Meta object you need in any place of your template. It supports single Meta objects (with :code:`gimme` ) and collections of Meta objects (with :code:`gimmelist`).


.. include:: /components/TemplatesSystem/features/customTags/gimme.rst
.. include:: /components/TemplatesSystem/features/customTags/gimmelist.rst


How to work with :code:`gimmelist` pagination?
----------------------------------------------

:code:`gimmelist` is based on Twig :code:`for` tag, like in Twig there is `loop <http://twig.sensiolabs.org/doc/tags/for.html#the-loop-variable>`_ variable available.
In addition to default loop properties there is also :code:`totalLength`. It's filled by loader with number of total elements in storage which are matching criteria. Thanks to this addition we can build real pagination.

:code:`TemplateEngine` Bundle provides simple default pagination template file: :code:`pagination.html.twig`.

.. note::

    You can override that template with :code:`SWPTemplatesSystemBundle/views/pagination.html.twig` file in Your theme. Or You can use own file used for pagination rendering.

Here is commented example of pagination:

.. code-block:: twig

    {# Setup list and pagination parameters #}
    {% set itemsPerPage, currentPage = 1, app.request.get('page', 1) %}
    {% set start = (currentPage / itemsPerPage) - 1 %}

    {# List all articles from route '/news' and limit them to `itemsPerPage` value starting from `start` value #}
    {% gimmelist article from articles|start(start)|limit(itemsPerPage) with {'route': '/news'} %}

        <li><a href="{{ url(article) }}">{{ article.title }} </a></li>

        {# Render pagination only at end of list #}
        {% if loop.last  %}
            {#
                Use provided by default pagination template

                Parameters:
                * currentFilters (array) : associative array that contains the current route-arguments
                * currentPage (int) : the current page you are in
                * paginationPath (Meta|string) : the route name (or supported by router Meta object) to use for links
                * lastPage (int) : represents the total number of existing pages
                * showAlwaysFirstAndLast (bool) : Always show first and last link (just disabled)
            #}
            {% include '@SWPTemplatesSystem/pagination.html.twig' with {
                currentFilters: {}|merge(app.request.query.all()),
                currentPage: currentPage,
                paginationPath: gimme.route,
                lastPage: (loop.totalLength/itemsPerPage)|round(1, 'ceil'),
                showAlwaysFirstAndLast: true
            } only %}
        {% endif %}
    {% endgimmelist %}

For referrence, see original *pagination.html.twig* template (if you want to customize it and use instead of default one):

.. code-block:: twig

    {#
      Source: http://dev.dbl-a.com/symfony-2-0/symfony2-and-twig-pagination/
      Updated by: Simon Schick <simonsimcity@gmail.com>

      Parameters:
        * currentFilters (array) : associative array that contains the current route-arguments
        * currentPage (int) : the current page you are in
        * paginationPath (string) : the route name to use for links
        * showAlwaysFirstAndLast (bool) : Always show first and last link (just disabled)
        * lastPage (int) : represents the total number of existing pages
    #}
    {% spaceless %}
        {% if lastPage > 1 %}

            {# the number of first and last pages to be displayed #}
            {% set extremePagesLimit = 3 %}

            {# the number of pages that are displayed around the active page #}
            {% set nearbyPagesLimit = 2 %}

            <nav class="pagination">
                <div class="numbers">
                    <ul>
                        {% if currentPage > 1 %}
                            <li><a href="{{ path(paginationPath, currentFilters|merge({page: currentPage-1})) }}">Previous</a></li>

                            {% for i in range(1, extremePagesLimit) if ( i < currentPage - nearbyPagesLimit ) %}
                                <li><a href="{{ path(paginationPath, currentFilters|merge({page: i})) }}">{{ i }}</a></li>
                            {% endfor %}

                            {% if extremePagesLimit + 1 < currentPage - nearbyPagesLimit %}
                                <span class="sep-dots">...</span>
                            {% endif %}

                            {% for i in range(currentPage-nearbyPagesLimit, currentPage-1) if ( i > 0 ) %}
                                <li><a href="{{ path(paginationPath, currentFilters|merge({page: i})) }}">{{ i }}</a></li>
                            {% endfor %}
                        {% elseif showAlwaysFirstAndLast %}
                            <span class="disabled">Previous</span>
                        {% endif %}

                        <li class="current"><a href="{{ path(paginationPath, currentFilters|merge({ page: currentPage })) }}">{{ currentPage }}</a></li>

                        {% if currentPage < lastPage %}
                            {% for i in range(currentPage+1, currentPage + nearbyPagesLimit) if ( i <= lastPage ) %}
                                <li><a href="{{ path(paginationPath, currentFilters|merge({page: i})) }}">{{ i }}</a></li>
                            {% endfor %}

                            {% if  (lastPage - extremePagesLimit) > (currentPage + nearbyPagesLimit) %}
                                <li><span class="sep-dots">...</span></li>
                            {% endif %}

                            {% for i in range(lastPage - extremePagesLimit+1, lastPage) if ( i > currentPage + nearbyPagesLimit ) %}
                                <li><a href="{{ path(paginationPath, currentFilters|merge({page: i})) }}">{{ i }}</a></li>
                            {% endfor %}

                            <li><a href="{{ path(paginationPath, currentFilters|merge({page: currentPage+1})) }}">Next</a></li>
                        {% elseif showAlwaysFirstAndLast %}
                            <li><span class="disabled">Next</span></li>
                        {% endif %}
                    </ul>
                </div>
            </nav>
        {% endif %}
    {% endspaceless %}

How to work with Meta objects
-----------------------------

On the template level, every variable in Context and fetched by :code:`gimme` and :code:`gimmelist` is a representation of Meta objects.


**dump**

.. code-block:: twig

    {{ dump(article) }}

**print**

.. code-block:: twig

    {{ article }}

If the meta configuration has the :code:`to_string` property then the value of this property will be printed, otherwise it will be represented as JSON.

**access property**

.. code-block:: twig

    {{ article.title }}
    {{ article['title']}}

**generate url**

.. code-block:: twig

    {{ url(article) }}    // absolute url
    {{ path(article) }}   // relative path

Here's an example using gimmelist:

.. code-block:: twig

    {% gimmelist article from articles %}
        <li><a href="{{ url(article) }}">{{ article.title }} </a></li>
    {% endgimmelist %}
