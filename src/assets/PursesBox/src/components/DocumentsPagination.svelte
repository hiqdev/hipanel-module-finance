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
  <div>
    {#if totalFiltered === 0}
      No documents
    {:else}
      Showing <strong>{from}–{to}</strong> of <strong>{totalFiltered}</strong>
      {#if totalFiltered !== totalBase}
        <span style="color: var(--fg-5)"> (filtered from {totalBase})</span>
      {/if}
    {/if}
  </div>

  <div class="pager">
    <button disabled={page === 1} onclick={() => onPage(page - 1)}>
      <i class="fa fa-angle-left"></i> Prev
    </button>
    {#each pages as p}
      <button class={page === p ? 'is-active' : ''} onclick={() => onPage(p)}>{p}</button>
    {/each}
    <button disabled={page === pageCount} onclick={() => onPage(page + 1)}>
      Next <i class="fa fa-angle-right"></i>
    </button>
  </div>
</div>
