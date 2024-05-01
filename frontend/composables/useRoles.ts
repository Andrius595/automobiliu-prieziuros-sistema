import {Roles} from "~/enums/roles";
import {asyncComputed} from "@vueuse/core/index";
import type {UserSession} from "~/types/userSession";

export const useRoles = () => {
    const auth = useAuth()
    const evaluating = ref(true);

    const hasClientRole = async (): Promise<boolean> => {
        const user: UserSession|null = await auth.getUser()

        if (!user) {
            return false
        }

        return user.roles.includes(Roles.CLIENT)
    }

    const hasServiceEmployeeRole = async (): Promise<boolean> => {
        const user: UserSession|null = await auth.getUser()

        if (!user) {
            return false
        }

        return user.roles.includes(Roles.SERVICE_EMPLOYEE)
    }

    const hasServiceAdminRole = async (): Promise<boolean> => {
        const user: UserSession|null = await auth.getUser()

        if (!user) {
            return false
        }

        return user.roles.includes(Roles.SERVICE_ADMIN)
    }

    const hasSystemAdminRole = async (): Promise<boolean> => {
        const user: UserSession|null = await auth.getUser()

        if (!user) {
            return false
        }

        return user.roles.includes(Roles.SYSTEM_ADMIN)
    }

    const isClientComputed = asyncComputed(async () => {
        let hasRole = await hasClientRole();
        evaluating.value = false;
        return hasRole
    }, false, evaluating)

    const isServiceEmployeeComputed = asyncComputed(async () => {
        let hasRole = await hasServiceEmployeeRole();
        evaluating.value = false;
        return hasRole
    }, false, evaluating)

    const isServiceAdminComputed = asyncComputed(async () => {
        let hasRole = await hasServiceAdminRole();
        evaluating.value = false;
        return hasRole
    }, false, evaluating)

    const isSystemAdminComputed = asyncComputed(async () => {
        let hasRole = await hasSystemAdminRole();
        evaluating.value = false;
        return hasRole
    }, false, evaluating)

    return {
        hasClientRole, isClientComputed,
        hasServiceEmployeeRole, isServiceEmployeeComputed,
        hasServiceAdminRole, isServiceAdminComputed,
        hasSystemAdminRole, isSystemAdminComputed
    }
}