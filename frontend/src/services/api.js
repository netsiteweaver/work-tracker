import axios from 'axios';

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';

const api = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  withCredentials: true,
});

// Add token to requests if available
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Handle 401 responses
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user');
      window.location.href = '/';
    }
    return Promise.reject(error);
  }
);

export default {
  // Auth
  register(data) {
    return api.post('/register', data);
  },
  login(data) {
    return api.post('/login', data);
  },
  logout() {
    return api.post('/logout');
  },
  getUser() {
    return api.get('/user');
  },

  // Projects
  getProjects() {
    return api.get('/projects');
  },
  getProject(id) {
    return api.get(`/projects/${id}`);
  },
  createProject(data) {
    return api.post('/projects', data);
  },
  updateProject(id, data) {
    return api.put(`/projects/${id}`, data);
  },
  deleteProject(id) {
    return api.delete(`/projects/${id}`);
  },
  updateProjectOrder(projects) {
    return api.post('/projects/update-order', { projects });
  },
};
