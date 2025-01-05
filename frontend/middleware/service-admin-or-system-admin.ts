import {useRoles} from "~/composables/useRoles";

export default defineNuxtRouteMiddleware(async (to, from) => {
    const { hasSystemAdminRole, hasServiceAdminRole } = useRoles()

    const isSystemAdmin = await hasSystemAdminRole()
    const isServiceAdmin = await hasServiceAdminRole()

    if (!isSystemAdmin && !isServiceAdmin) {
        return navigateTo('/services');
    }
})