<script lang="ts">
  let { files, onClose }: {
      files: string[];
      onClose: () => void;
  } = $props();

  let activeIdx = $state(0);

  function tabLabel(url: string, idx: number): string {
    try {
      const name = new URL(url).pathname.split("/").pop() ?? "";
      return name || `File ${idx + 1}`;
    } catch {
      return `File ${idx + 1}`;
    }
  }
</script>

<div class="modal-backdrop fade in"></div>

<!-- svelte-ignore a11y_no_static_element_interactions -->
<div
    class="modal fade in"
    style="display:block"
    role="dialog"
    aria-modal="true"
    tabindex="-1"
    onclick={onClose}
    onkeydown={() => {}}
>
  <!-- svelte-ignore a11y_no_noninteractive_element_interactions -->
  <div class="modal-dialog modal-lg" role="document" onclick={(e) => e.stopPropagation()} onkeydown={() => {}}>
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" onclick={onClose} aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title"><i class="fa fa-eye"></i> Preview</h4>
      </div>

      <div class="modal-body preview-modal-body">
        {#if files.length === 0}
          <div class="preview-no-files">
            <i class="fa fa-file-text-o"></i>
            <span>No preview available</span>
          </div>
        {:else if files.length === 1}
          <iframe src={files[0]} class="preview-iframe" title="Document preview"></iframe>
        {:else}
          <div class="preview-tabs">
            <ul class="preview-tab-list" role="tablist">
              {#each files as url, idx (url)}
                <li role="presentation">
                  <button
                      type="button"
                      role="tab"
                      class="preview-tab {activeIdx === idx ? 'is-active' : ''}"
                      aria-selected={activeIdx === idx}
                      onclick={() => { activeIdx = idx; }}
                  >
                    <i class="fa fa-file-text-o"></i>
                    {tabLabel(url, idx)}
                  </button>
                </li>
              {/each}
            </ul>
            {#each files as url, idx (url)}
              <iframe
                  src={url}
                  class="preview-iframe"
                  title="Document preview {idx + 1}"
                  hidden={activeIdx !== idx}
              ></iframe>
            {/each}
          </div>
        {/if}
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" onclick={onClose}>
          <i class="fa fa-times"></i> Close
        </button>
      </div>
    </div>
  </div>
</div>
