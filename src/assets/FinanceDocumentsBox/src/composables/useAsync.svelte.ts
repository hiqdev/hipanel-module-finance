export function useAsync<T>(fn: () => Promise<T>, options?: { lazy?: boolean }) {
  let data = $state<T | null>(null);
  let error = $state<string | null>(null);
  let loading = $state(false);

  async function run() {
    loading = true;
    error = null;
    try {
      data = await fn();
    } catch (e: any) {
      error = e.message ?? "Unknown error";
    } finally {
      loading = false;
    }
  }

  if (!options?.lazy) {
    run();
  }

  return {
    get data() {
      return data;
    },
    get error() {
      return error;
    },
    get loading() {
      return loading;
    },
    refetch: run,
  };
}
