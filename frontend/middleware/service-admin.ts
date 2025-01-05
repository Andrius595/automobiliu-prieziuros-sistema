import {useJWT} from "~/composables/useJWT";
import {useRoles} from "~/composables/useRoles";

export default defineNuxtRouteMiddleware(async (to, from) => {
    const { hasServiceAdminRole } = useRoles()

    const isServiceAdmin = await hasServiceAdminRole()

    if (!isServiceAdmin) {
        await navigateTo('/dashboard');
    }
})