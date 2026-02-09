import { ref, computed } from 'vue';
import api from '../services/api';

const user = ref(null);
const token = ref(localStorage.getItem('auth_token'));

// Initialize user from localStorage if token exists
if (token.value) {
  const storedUser = localStorage.getItem('user');
  if (storedUser) {
    try {
      user.value = JSON.parse(storedUser);
    } catch (e) {
      console.error('Failed to parse stored user:', e);
    }
  }
}

export function useAuth() {
  const isAuthenticated = computed(() => !!token.value && !!user.value);

  const login = async (email, password) => {
    try {
      const response = await api.login({ email, password });
      token.value = response.data.token;
      user.value = response.data.user;
      localStorage.setItem('auth_token', token.value);
      localStorage.setItem('user', JSON.stringify(user.value));
      return { success: true };
    } catch (error) {
      console.error('Login error:', error);
      return {
        success: false,
        error: error.response?.data?.message || 'Login failed',
      };
    }
  };

  const register = async (name, email, password, password_confirmation) => {
    try {
      const response = await api.register({
        name,
        email,
        password,
        password_confirmation,
      });
      token.value = response.data.token;
      user.value = response.data.user;
      localStorage.setItem('auth_token', token.value);
      localStorage.setItem('user', JSON.stringify(user.value));
      return { success: true };
    } catch (error) {
      console.error('Registration error:', error);
      return {
        success: false,
        error: error.response?.data?.message || 'Registration failed',
      };
    }
  };

  const logout = async () => {
    try {
      await api.logout();
    } catch (error) {
      console.error('Logout error:', error);
    } finally {
      token.value = null;
      user.value = null;
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user');
    }
  };

  const fetchUser = async () => {
    if (!token.value) return;
    try {
      const response = await api.getUser();
      user.value = response.data;
      localStorage.setItem('user', JSON.stringify(user.value));
    } catch (error) {
      console.error('Fetch user error:', error);
      // Clear auth if fetching user fails
      token.value = null;
      user.value = null;
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user');
    }
  };

  return {
    user,
    isAuthenticated,
    login,
    register,
    logout,
    fetchUser,
  };
}
