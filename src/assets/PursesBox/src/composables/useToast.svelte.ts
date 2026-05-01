export function useToast() {
  function show(msg: string, type: "success" | "error" = "success") {
    window.hipanel.notify[type](msg);
  }

  return {
    show,
  };
}
