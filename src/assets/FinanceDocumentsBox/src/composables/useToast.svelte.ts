export type ToastType = "success" | "error";

export function useToast() {
  function show(msg: string, type: ToastType = "success") {
    window.hipanel.notify[type](msg);
  }

  return {
    show,
  };
}
