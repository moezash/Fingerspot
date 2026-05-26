export function notImplemented(method: string): Promise<never> {
  return Promise.reject(
    new Error(`[Eldev API] ${method} is not implemented yet.`)
  );
}
