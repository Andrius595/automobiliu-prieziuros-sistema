export const useSnackbar = () => {
    const visible = useState('snackbar', () => false)
    const message = useState('snackbar_message', () => '');

    const show = (_message: string) => {
        message.value = _message;
        visible.value = true;
    }
    const hide = () => {
        visible.value = false;
        message.value = '';
    }

    const isVisible = () => visible.value
    const getMessage = () => message.value

    return { show, hide, isVisible, getMessage }
}