export const useLayoutSidebar = () => {
    const isSidebarOpen = useLocalStorage<boolean>('isSidebarOpen', () => false);

    function toggleSidebar() {
        isSidebarOpen.value = !isSidebarOpen.value;
    }

    function setSidebarValue(value: boolean) {
        isSidebarOpen.value = value;
    }

    return { isSidebarOpen, toggleSidebar, setSidebarValue };
}