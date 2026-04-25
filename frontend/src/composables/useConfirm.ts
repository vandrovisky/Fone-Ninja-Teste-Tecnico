import { ref } from 'vue';

interface ConfirmOptions {
  title: string;
  message: string;
  confirmText?: string;
  cancelText?: string;
  variant?: 'danger' | 'warning' | 'info';
}

const isOpen = ref(false);
const options = ref<ConfirmOptions>({
  title: '',
  message: '',
});

let resolvePromise: (value: boolean) => void;

export function useConfirm() {
  const confirm = (opts: ConfirmOptions): Promise<boolean> => {
    options.value = {
      ...opts,
      confirmText: opts.confirmText || 'Confirmar',
      cancelText: opts.cancelText || 'Cancelar',
      variant: opts.variant || 'info',
    };
    isOpen.value = true;
    return new Promise((resolve) => {
      resolvePromise = resolve;
    });
  };

  const handleConfirm = () => {
    isOpen.value = false;
    resolvePromise(true);
  };

  const handleCancel = () => {
    isOpen.value = false;
    resolvePromise(false);
  };

  return {
    isOpen,
    options,
    confirm,
    handleConfirm,
    handleCancel,
  };
}
