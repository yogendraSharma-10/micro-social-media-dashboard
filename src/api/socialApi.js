import axios from 'axios';

/**
 * @file src/api/socialApi.js
 * @description API client for interacting with the Micro Social Media Dashboard backend.
 * This module provides functions to make HTTP requests for posts, users, notifications, and authentication.
 * It uses Axios for robust request handling, including base URL configuration,
 * request/response interceptors, and error handling.
 */

// --- Configuration ---
// Base URL for the Social Media API.
// This should be set in your .env file (e.g., REACT_APP_SOCIAL_API_BASE_URL=http://localhost:8000/api)
const SOCIAL_API_BASE_URL = process.env.REACT_APP_SOCIAL_API_BASE_URL || 'http://localhost:8000/api';

// Base URL for the E-commerce Marketplace API (for cross-project context).
// This is an example and might be used if user profiles or shared services
// need to interact with the marketplace backend.
// const ECOMMERCE_API_BASE_URL = process.env.REACT_APP_ECOMMERCE_API_BASE_URL || 'http://localhost:8001/api';

// --- Axios Instance ---
const socialApiClient = axios.create({
  baseURL: SOCIAL_API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  withCredentials: true, // Important for sending cookies (e.g., session, CSRF tokens)
});

// --- Request Interceptor ---
// Automatically attach authorization token to requests if available.
socialApiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('authToken'); // Assuming JWT or similar token storage
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// --- Response Interceptor ---
// Handle common response errors, e.g., unauthorized, forbidden.
socialApiClient.interceptors.response.use(
  (response) => {
    return response;
  },
  (error) => {
    if (error.response) {
      // The request was made and the server responded with a status code
      // that falls out of the range of 2xx
      console.error('API Error:', error.response.status, error.response.data);
      if (error.response.status === 401 || error.response.status === 403) {
        // Handle unauthorized or forbidden errors, e.g., redirect to login
        console.warn('Authentication error: User might need to log in again.');
        // Example: window.location.href = '/login';
      }
    } else if (error.request) {
      // The request was made but no response was received
      console.error('Network Error: No response received from API.', error.request);
    } else {
      // Something happened in setting up the request that triggered an Error
      console.error('Request Setup Error:', error.message);
    }
    return Promise.reject(error);
  }
);

// --- API Functions ---

/**
 * @namespace Auth
 * @description Functions related to user authentication.
 */
export const Auth = {
  /**
   * Registers a new user.
   * @param {object} userData - User registration data (e.g., { name, email, password, password_confirmation }).
   * @returns {Promise<object>} Response data containing user info and/or token.
   */
  register: async (userData) => {
    try {
      const response = await socialApiClient.post('/auth/register', userData);
      // Assuming the backend returns a token upon successful registration
      if (response.data.token) {
        localStorage.setItem('authToken', response.data.token);
      }
      return response.data;
    } catch (error) {
      console.error('Registration failed:', error);
      throw error;
    }
  },

  /**
   * Logs in an existing user.
   * @param {object} credentials - User login credentials (e.g., { email, password }).
   * @returns {Promise<object>} Response data containing user info and/or token.
   */
  login: async (credentials) => {
    try {
      const response = await socialApiClient.post('/auth/login', credentials);
      // Assuming the backend returns a token upon successful login
      if (response.data.token) {
        localStorage.setItem('authToken', response.data.token);
      }
      return response.data;
    } catch (error) {
      console.error('Login failed:', error);
      throw error;
    }
  },

  /**
   * Logs out the current user.
   * @returns {Promise<object>} Response data.
   */
  logout: async () => {
    try {
      const response = await socialApiClient.post('/auth/logout');
      localStorage.removeItem('authToken'); // Clear token on logout
      return response.data;
    } catch (error) {
      console.error('Logout failed:', error);
      throw error;
    }
  },

  /**
   * Fetches the authenticated user's profile.
   * @returns {Promise<object>} User profile data.
   */
  me: async () => {
    try {
      const response = await socialApiClient.get('/auth/me');
      return response.data;
    } catch (error) {
      console.error('Failed to fetch authenticated user:', error);
      throw error;
    }
  },
};

/**
 * @namespace Posts
 * @description Functions related to social media posts.
 */
export const Posts = {
  /**
   * Fetches all posts from the feed.
   * @param {number} [page=1] - The page number for pagination.
   * @param {number} [limit=10] - The number of posts per page.
   * @returns {Promise<object>} A paginated list of posts.
   */
  getFeed: async (page = 1, limit = 10) => {
    try {
      const response = await socialApiClient.get(`/posts?page=${page}&limit=${limit}`);
      return response.data;
    } catch (error) {
      console.error('Failed to fetch post feed:', error);
      throw error;
    }
  },

  /**
   * Fetches posts by a specific user.
   * @param {string} userId - The ID of the user whose posts to fetch.
   * @param {number} [page=1] - The page number for pagination.
   * @param {number} [limit=10] - The number of posts per page.
   * @returns {Promise<object>} A paginated list of user's posts.
   */
  getUserPosts: async (userId, page = 1, limit = 10) => {
    try {
      const response = await socialApiClient.get(`/users/${userId}/posts?page=${page}&limit=${limit}`);
      return response.data;
    } catch (error) {
      console.error(`Failed to fetch posts for user ${userId}:`, error);
      throw error;
    }
  },

  /**
   * Creates a new post.
   * @param {object} postData - The data for the new post (e.g., { content, image_url }).
   * @returns {Promise<object>} The newly created post.
   */
  createPost: async (postData) => {
    try {
      const response = await socialApiClient.post('/posts', postData);
      return response.data;
    } catch (error) {
      console.error('Failed to create post:', error);
      throw error;
    }
  },

  /**
   * Likes a specific post.
   * @param {string} postId - The ID of the post to like.
   * @returns {Promise<object>} Confirmation of the like action.
   */
  likePost: async (postId) => {
    try {
      const response = await socialApiClient.post(`/posts/${postId}/like`);
      return response.data;
    } catch (error) {
      console.error(`Failed to like post ${postId}:`, error);
      throw error;
    }
  },

  /**
   * Unlikes a specific post.
   * @param {string} postId - The ID of the post to unlike.
   * @returns {Promise<object>} Confirmation of the unlike action.
   */
  unlikePost: async (postId) => {
    try {
      const response = await socialApiClient.delete(`/posts/${postId}/like`);
      return response.data;
    } catch (error) {
      console.error(`Failed to unlike post ${postId}:`, error);
      throw error;
    }
  },

  /**
   * Adds a comment to a specific post.
   * @param {string} postId - The ID of the post to comment on.
   * @param {object} commentData - The comment data (e.g., { content }).
   * @returns {Promise<object>} The newly created comment.
   */
  addComment: async (postId, commentData) => {
    try {
      const response = await socialApiClient.post(`/posts/${postId}/comments`, commentData);
      return response.data;
    } catch (error) {
      console.error(`Failed to add comment to post ${postId}:`, error);
      throw error;
    }
  },

  /**
   * Deletes a specific post.
   * @param {string} postId - The ID of the post to delete.
   * @returns {Promise<object>} Confirmation of the deletion.
   */
  deletePost: async (postId) => {
    try {
      const response = await socialApiClient.delete(`/posts/${postId}`);
      return response.data;
    } catch (error) {
      console.error(`Failed to delete post ${postId}:`, error);
      throw error;
    }
  },
};

/**
 * @namespace Users
 * @description Functions related to user profiles and interactions.
 */
export const Users = {
  /**
   * Fetches a user's profile by ID.
   * @param {string} userId - The ID of the user to fetch.
   * @returns {Promise<object>} The user's profile data.
   */
  getProfile: async (userId) => {
    try {
      const response = await socialApiClient.get(`/users/${userId}`);
      return response.data;
    } catch (error) {
      console.error(`Failed to fetch user profile ${userId}:`, error);
      throw error;
    }
  },

  /**
   * Follows a user.
   * @param {string} userIdToFollow - The ID of the user to follow.
   * @returns {Promise<object>} Confirmation of the follow action.
   */
  followUser: async (userIdToFollow) => {
    try {
      const response = await socialApiClient.post(`/users/${userIdToFollow}/follow`);
      return response.data;
    } catch (error) {
      console.error(`Failed to follow user ${userIdToFollow}:`, error);
      throw error;
    }
  },

  /**
   * Unfollows a user.
   * @param {string} userIdToUnfollow - The ID of the user to unfollow.
   * @returns {Promise<object>} Confirmation of the unfollow action.
   */
  unfollowUser: async (userIdToUnfollow) => {
    try {
      const response = await socialApiClient.delete(`/users/${userIdToUnfollow}/follow`);
      return response.data;
    } catch (error) {
      console.error(`Failed to unfollow user ${userIdToUnfollow}:`, error);
      throw error;
    }
  },

  /**
   * Searches for users by a query string.
   * @param {string} query - The search query.
   * @returns {Promise<object[]>} A list of matching users.
   */
  searchUsers: async (query) => {
    try {
      const response = await socialApiClient.get(`/users/search?q=${encodeURIComponent(query)}`);
      return response.data;
    } catch (error) {
      console.error(`Failed to search users with query "${query}":`, error);
      throw error;
    }
  },

  /**
   * Updates the current user's profile.
   * @param {object} profileData - The data to update (e.g., { name, bio, profile_picture_url }).
   * @returns {Promise<object>} The updated user profile.
   */
  updateProfile: async (profileData) => {
    try {
      // Assuming the backend has an endpoint for updating the authenticated user's profile
      const response = await socialApiClient.put('/users/me', profileData);
      return response.data;
    } catch (error) {
      console.error('Failed to update user profile:', error);
      throw error;
    }
  },
};

/**
 * @namespace Notifications
 * @description Functions related to user notifications.
 */
export const Notifications = {
  /**
   * Fetches all notifications for the authenticated user.
   * @param {boolean} [unreadOnly=false] - If true, only fetches unread notifications.
   * @returns {Promise<object[]>} A list of notifications.
   */
  getNotifications: async (unreadOnly = false) => {
    try {
      const response = await socialApiClient.get(`/notifications?unread_only=${unreadOnly}`);
      return response.data;
    } catch (error) {
      console.error('Failed to fetch notifications:', error);
      throw error;
    }
  },

  /**
   * Marks a specific notification as read.
   * @param