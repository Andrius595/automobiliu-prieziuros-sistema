import {useJWT} from "~/composables/useJWT";
import {useRoles} from "~/composables/useRoles";

export default defineNuxtRouteMiddleware(async (to, from) => {
    const { isServiceAdmin, isClient } = useRoles()

    if (!isServiceAdmin.value || !isClient.value) {
        await navigateTo('/dashboard');
    }
})