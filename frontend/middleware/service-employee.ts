import {useJWT} from "~/composables/useJWT";
import {useRoles} from "~/composables/useRoles";

export default defineNuxtRouteMiddleware(async (to, from) => {
    const { hasServiceEmployeeRole, hasServiceAdminRole } = useRoles()

    if (!await hasServiceEmployeeRole() && !await hasServiceAdminRole()) {
        return navigateTo('/cars');
    }
})