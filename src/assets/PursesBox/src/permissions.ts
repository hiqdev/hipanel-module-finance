import type { PermissionKey } from "./types.ts";

class Permissions {
  private granted: ReadonlySet<PermissionKey> = new Set();

  init(keys: PermissionKey[]): void {
    this.granted = new Set(keys);
  }

  can(key: PermissionKey): boolean {
    return this.granted.has(key);
  }
}

export const permissions = new Permissions();
