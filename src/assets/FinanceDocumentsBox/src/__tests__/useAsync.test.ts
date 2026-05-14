import { describe, expect, it } from "vitest";
import { useAsync } from "../composables/useAsync.svelte";

describe("useAsync", () => {
  it("starts with no data and no loading when lazy", () => {
    const hook = useAsync(() => Promise.resolve("x"), { lazy: true });
    expect(hook.data).toBeNull();
    expect(hook.loading).toBe(false);
    expect(hook.error).toBeNull();
  });

  it("stores resolved data after refetch", async () => {
    const hook = useAsync(() => Promise.resolve({ id: 1 }), { lazy: true });
    await hook.refetch();
    expect(hook.data).toEqual({ id: 1 });
    expect(hook.loading).toBe(false);
    expect(hook.error).toBeNull();
  });

  it("stores error message on rejection", async () => {
    const hook = useAsync(() => Promise.reject(new Error("oops")), { lazy: true });
    await hook.refetch();
    expect(hook.error).toBe("oops");
    expect(hook.data).toBeNull();
  });

  it("clears previous error on successful refetch", async () => {
    let fail = true;
    const hook = useAsync(
      () => (fail ? Promise.reject(new Error("fail")) : Promise.resolve("ok")),
      { lazy: true },
    );
    await hook.refetch();
    expect(hook.error).toBe("fail");
    fail = false;
    await hook.refetch();
    expect(hook.error).toBeNull();
    expect(hook.data).toBe("ok");
  });
});
