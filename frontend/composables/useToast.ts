import {toast} from "vuetify-sonner";

export const useToast = () => {
    const { t } = useI18n()
    function successToast(message: string) {
        return toast.success(message, {
            duration: 5000,
            id: 's'+Math.random().toString(36).substring(2),
            onAutoClose: () => {},
            onDismiss: () => {},
            action: {
                label: t('common.close'),
                onClick: () => {},
            },
            important: true,
        })
    }

    function errorToast(message: string) {
        return toast.error(message, {
            duration: 5000,
            id: 'e'+Math.random().toString(36).substring(2),
            onAutoClose: () => {},
            onDismiss: () => {},
            action: {
                label: t('common.close'),
                onClick: () => {},
            },
            important: true,
        })
    }


    return { successToast, errorToast }
}