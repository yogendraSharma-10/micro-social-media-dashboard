import React, { useState, useEffect, useCallback } from 'react';
import socialApi from '../api/socialApi';
import PostItem from './PostItem'; // Assuming PostItem component exists for individual post rendering
import LoadingSpinner from '../common/LoadingSpinner'; // Assuming a common LoadingSpinner component
import ErrorDisplay from '../common/ErrorDisplay'; // Assuming a common ErrorDisplay component
import '../styles/main.css'; // Main application styles

/**
 * PostFeed Component
 *
 * Displays a feed of social media posts. It fetches posts from the backend
 * and renders them using the PostItem component. Includes loading and error states.
 *
 * @param {object} props - Component props
 * @param {string} [props.feedType='global'] - Type of feed to display ('global', 'following', 'user').
 *                                            'global' fetches all posts, 'following' fetches posts from followed users,
 *                                            'user' fetches posts for a specific user (requires userId).
 * @param {number} [props.userId] - User ID to fetch posts for if feedType is 'user'.
 */
const PostFeed = ({ feedType = 'global', userId = null }) => {
  const [posts, setPosts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [page, setPage] = useState(1);
  const [hasMore, setHasMore] = useState(true);

  // Function to fetch posts based on feed type and pagination
  const fetchPosts = useCallback(async (currentPage) => {
    setLoading(true);
    setError(null);
    try {
      let response;
      switch (feedType) {
        case 'following':
          // In a real app, this would require the current authenticated user's ID
          // For now, let's assume socialApi.getFollowingPosts handles it internally or takes a user ID
          response = await socialApi.getFollowingPosts(currentPage);
          break;
        case 'user':
          if (!userId) {
            throw new Error("userId is required for 'user' feedType.");
          }
          response = await socialApi.getUserPosts(userId, currentPage);
          break;
        case 'global':
        default:
          response = await socialApi.getPosts(currentPage);
          break;
      }

      // Assuming the API returns an object with `data` (array of posts) and `meta` (pagination info)
      const newPosts = response.data || [];
      const totalPages = response.meta?.last_page || 1;

      setPosts(prevPosts => currentPage === 1 ? newPosts : [...prevPosts, ...newPosts]);
      setHasMore(currentPage < totalPages);

    } catch (err) {
      console.error('Failed to fetch posts:', err);
      setError('Failed to load posts. Please try again later.');
    } finally {
      setLoading(false);
    }
  }, [feedType, userId]);

  // Initial fetch on component mount or when feedType/userId changes
  useEffect(() => {
    setPosts([]); // Clear posts when feed type or user changes
    setPage(1); // Reset page to 1
    setHasMore(true); // Assume there's more data initially
    fetchPosts(1);
  }, [feedType, userId, fetchPosts]);

  // Handle infinite scrolling or "Load More" button
  const handleLoadMore = () => {
    if (!loading && hasMore) {
      setPage(prevPage => prevPage + 1);
    }
  };

  // Effect to fetch more posts when page state changes (e.g., from handleLoadMore)
  useEffect(() => {
    if (page > 1) {
      fetchPosts(page);
    }
  }, [page, fetchPosts]);

  // Render loading state
  if (loading && posts.length === 0) {
    return (
      <div className="post-feed-container">
        <LoadingSpinner message="Loading posts..." />
      </div>
    );
  }

  // Render error state
  if (error) {
    return (
      <div className="post-feed-container">
        <ErrorDisplay message={error} />
      </div>
    );
  }

  return (
    <div className="post-feed-container">
      {posts.length === 0 && !loading && (
        <p className="no-posts-message">No posts to display yet. Be the first to post!</p>
      )}

      <div className="post-list">
        {posts.map(post => (
          <PostItem key={post.id} post={post} />
        ))}
      </div>

      {hasMore && (
        <div className="load-more-section">
          <button
            onClick={handleLoadMore}
            disabled={loading}
            className="btn btn-primary load-more-btn"
          >
            {loading ? 'Loading More...' : 'Load More Posts'}
          </button>
        </div>
      )}
    </div>
  );
};

export default PostFeed;