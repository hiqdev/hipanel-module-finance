import { beforeEach, describe, expect, it } from "vitest";
import { permissions } from "../permissions";

beforeEach(() => {
  permissions.init([]);
});

describe("permissions", () => {
  it("grants a permission that was initialised", () => {
    permissions.init(["purse.update", "owner-staff"]);
    expect(permissions.can("purse.update")).toBe(true);
    expect(permissions.can("owner-staff")).toBe(true);
  });

  it("denies an unknown permission", () => {
    permissions.init(["purse.update"]);
    expect(permissions.can("admin")).toBe(false);
  });

  it("denies everything after init with empty array", () => {
    permissions.init([]);
    expect(permissions.can("purse.update")).toBe(false);
  });

  it("does not throw when init receives null", () => {
    expect(() => permissions.init(null as any)).not.toThrow();
    expect(permissions.can("purse.update")).toBe(false);
  });

  it("second init overwrites the first", () => {
    permissions.init(["purse.update"]);
    permissions.init(["owner-staff"]);
    expect(permissions.can("purse.update")).toBe(false);
    expect(permissions.can("owner-staff")).toBe(true);
  });
});
