import {useRoles} from "~/composables/useRoles";

export default defineNuxtRouteMiddleware(async (to, from) => {
    const { hasSystemAdminRole, hasClientRole } = useRoles()

    const isSystemAdmin = await hasSystemAdminRole()
    const isClient = await hasClientRole()

    if (!isSystemAdmin && !isClient) {
        return navigateTo('/services/registrations');
    }
})