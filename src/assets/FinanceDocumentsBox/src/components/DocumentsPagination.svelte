<script lang="ts">
  let { page, pageCount, totalFiltered, totalBase, pageSize, onPage }: {
      page: number;
      pageCount: number;
      totalFiltered: number;
      totalBase: number;
      pageSize: number;
      onPage: (p: number) => void;
  } = $props();

  let from = $derived((page - 1) * pageSize + 1);
  let to = $derived(Math.min(page * pageSize, totalFiltered));
  let pages = $derived(Array.from({ length: pageCount }, (_, i) => i + 1));
</script>

<div class="docs-foot">
  <div class="docs-foot-count">
    {#if totalFiltered === 0}
      No documents
    {:else}
      Showing <strong>{from}–{to}</strong> of <strong>{totalFiltered}</strong>
        {#if totalFiltered !== totalBase}
        <span class="text-muted"> (filtered from {totalBase})</span>
      {/if}
    {/if}
  </div>

    {#if pageCount > 1}
    <ul class="pagination pagination-sm" style="margin: 0">
      <li class={page === 1 ? 'disabled' : ''}>
        <!-- svelte-ignore a11y_invalid_attribute -->
        <a href="#" aria-label="Previous page" onclick={(e) => { e.preventDefault(); if (page > 1) onPage(page - 1); }}>
          <i class="fa fa-angle-left"></i>
        </a>
      </li>
        {#each pages as p}
        <li class={p === page ? 'active' : ''}>
          <!-- svelte-ignore a11y_invalid_attribute -->
          <a href="#" onclick={(e) => { e.preventDefault(); onPage(p); }}>{p}</a>
        </li>
      {/each}
        <li class={page === pageCount ? 'disabled' : ''}>
        <!-- svelte-ignore a11y_invalid_attribute -->
        <a href="#" aria-label="Next page" onclick={(e) => { e.preventDefault(); if (page < pageCount) onPage(page + 1); }}>
          <i class="fa fa-angle-right"></i>
        </a>
      </li>
    </ul>
  {/if}
</div>
