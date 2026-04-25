import { ref } from 'vue';

export type ToastType = 'success' | 'error' | 'info' | 'warning';

export interface Toast {
  id: number;
  type: ToastType;
  message: string;
  duration?: number;
}

const toasts = ref<Toast[]>([]);
let nextId = 0;

export function useToast() {
  const addToast = (type: ToastType, message: string, duration = 4000) => {
    const id = nextId++;
    toasts.value.push({ id, type, message, duration });

    setTimeout(() => {
      removeToast(id);
    }, duration);
  };

  const removeToast = (id: number) => {
    const index = toasts.value.findIndex((t) => t.id === id);
    if (index !== -1) {
      toasts.value.splice(index, 1);
    }
  };

  const success = (message: string, duration?: number) => addToast('success', message, duration);
  const error = (message: string, duration?: number) => addToast('error', message, duration ?? 5000);
  const warning = (message: string, duration?: number) => addToast('warning', message, duration);
  const info = (message: string, duration?: number) => addToast('info', message, duration);

  return { toasts, addToast, removeToast, success, error, warning, info };
}
