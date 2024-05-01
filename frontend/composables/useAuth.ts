import {type UserSession} from "~/types/userSession";
import {useJWT} from "~/composables/useJWT";

export const useAuth = () => {
    const _user = useState<UserSession>('user')
    const jwt = useJWT()

    const setUser = (user: UserSession) => {
        _user.value = user
    }

    const fetchUser = async () => {
        try {
            const { data } = await backFetch<UserSession|null>('/auth/user', {method: 'get', headers: {Accept: 'application/json'}})

            return data.value
        } catch (e) {
            return null;
        }
    }

    const getUser = async (forceFetch = false): Promise<UserSession|null> => {
        if (forceFetch || _user.value === undefined || _user.value === null) {
            if (!jwt.getToken()) {
                return null
            }
            const user = await fetchUser()
            if (user && 'id' in user) {
                setUser(user)
            }
        }

        return _user.value
    }

    const logout = async () => {
        await backFetch('/auth/logout', {method: 'post'})

        jwt.setToken('')
        _user.value = undefined as unknown as UserSession
        await navigateTo('/login')
    }

    return { getUser, logout }
}