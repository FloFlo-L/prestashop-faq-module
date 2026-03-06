{extends file='page.tpl'}

{block name='page_header_container'}
  <div class="faq-page-header">
    <h1 class="faq-page-title">
      {if $faq_title}{$faq_title|escape:'html':'UTF-8'}{else}{l s='Frequently Asked Questions' mod='faq'}{/if}
    </h1>
    {if $faq_subtitle}
      <p class="faq-page-subtitle">{$faq_subtitle|escape:'html':'UTF-8'}</p>
    {/if}
  </div>
{/block}

{block name='page_content'}
  {if empty($faq_categories)}
    <p class="alert alert-info">{l s='No FAQ available at the moment.' mod='faq'}</p>
  {else}

    {* ── Barre de catégories ── *}
    <div class="faq-categories-scroll">
      <div class="faq-categories-nav" role="tablist">
        {foreach from=$faq_categories item=category name=cats}
          <button
            class="faq-category-tab{if $smarty.foreach.cats.first} active{/if}"
            type="button"
            data-bs-toggle="tab"
            data-bs-target="#faq-tab-{$smarty.foreach.cats.index}"
          >
            {if $category.icon}
              <span class="faq-tab-icon material-icons">{$category.icon|escape:'html':'UTF-8'}</span>
            {/if}
            <span class="faq-tab-label">{$category.name|escape:'html':'UTF-8'}</span>
          </button>
        {/foreach}
      </div>
    </div>

    {* ── Contenu des onglets ── *}
    <div class="faq-tabs-content">
      {foreach from=$faq_categories item=category name=cats}
        <div
          class="faq-tab-pane{if $smarty.foreach.cats.first} active{/if}"
          id="faq-tab-{$smarty.foreach.cats.index}"
        >
          <div class="faq-accordion pb-3" id="faq-accordion-{$smarty.foreach.cats.index}">
            {foreach from=$category.questions item=faq name=faqs}
              {assign var=collapseId value="faq-collapse-{$smarty.foreach.cats.index}-{$smarty.foreach.faqs.index}"}
              <div class="faq-accordion-item">
                <button
                  class="faq-accordion-question"
                  type="button"
                  data-faq-target="{$collapseId}"
                  aria-expanded="false"
                >
                  {$faq.question|escape:'html':'UTF-8'}
                  <span class="faq-chevron material-icons">expand_more</span>
                </button>
                <div
                  id="{$collapseId}"
                  class="faq-accordion-body"
                >
                  <div class="faq-accordion-answer">
                    {$faq.answer nofilter}
                  </div>
                </div>
              </div>
            {/foreach}
          </div>
        </div>
      {/foreach}
    </div>

  {/if}
{/block}

