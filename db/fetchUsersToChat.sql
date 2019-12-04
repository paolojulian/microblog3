BEGIN
SELECT 
    users.id, users.username, users.avatar_url,
    users.first_name,
    users.last_name,
    c.message
FROM
    users
INNER JOIN
followers ON followers.following_id = users.id
LEFT JOIN (
	SELECT chats.message, chats.created, chats.is_read, chats.user_id, chats.receiver_id
    FROM chats
    WHERE chats.receiver_id = userId
    AND chats.deleted IS NULL
    ORDER BY chats.created DESC
    LIMIT 1
) c
ON c.user_id = users.id

WHERE followers.user_id = userId
AND followers.deleted IS NULL
AND users.deleted IS NULL
GROUP BY users.id
ORDER BY c.created DESC, followers.created DESC
LIMIT perPage
OFFSET pageOffset;

END