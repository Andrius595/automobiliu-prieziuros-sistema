import {useJWT} from "~/composables/useJWT";
import {useRoles} from "~/composables/useRoles";

export default defineNuxtRouteMiddleware(async (to, from) => {
    const { isServiceAdmin } = useRoles()

    if (!isServiceAdmin.value) {
        await navigateTo('/dashboard');
    }
})