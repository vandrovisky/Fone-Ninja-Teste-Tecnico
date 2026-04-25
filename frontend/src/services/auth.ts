import { reactive, watch } from 'vue';
import api from './api';

interface AuthState {
  user: any | null;
  token: string | null;
  isAuthenticated: boolean;
}

const STORAGE_KEY = 'fone_ninja_auth';

const storedAuth = localStorage.getItem(STORAGE_KEY);
const initialState: AuthState = storedAuth ? JSON.parse(storedAuth) : {
  user: null,
  token: null,
  isAuthenticated: false,
};

export const auth = reactive<AuthState>(initialState);

watch(auth, (newState) => {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(newState));
  if (newState.token) {
    api.defaults.headers.common['Authorization'] = `Bearer ${newState.token}`;
  } else {
    delete api.defaults.headers.common['Authorization'];
  }
}, { deep: true });

// Initialize header if token exists
if (auth.token) {
  api.defaults.headers.common['Authorization'] = `Bearer ${auth.token}`;
}

export const useAuth = () => {
  const login = async (credentials: any) => {
    const response = await api.post('/login', credentials);
    auth.token = response.data.token;
    auth.user = response.data.user;
    auth.isAuthenticated = true;
    return response.data;
  };

  const register = async (data: any) => {
    const response = await api.post('/register', data);
    auth.token = response.data.token;
    auth.user = response.data.user;
    auth.isAuthenticated = true;
    return response.data;
  };

  const logout = async () => {
    const token = auth.token;

    auth.token = null;
    auth.user = null;
    auth.isAuthenticated = false;
    localStorage.removeItem(STORAGE_KEY);
    delete api.defaults.headers.common['Authorization'];

    if (!token) {
      return;
    }

    try {
      await api.post('/logout', {}, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });
    } catch (e) {
      // Ignore logout error if token is already invalid
    }
  };

  return { auth, login, register, logout };
};
