const http = require('http');
const url = require('url');
const queryString = require('query-string');
const WebSocketServerPort = 4567;
const WebSocket = require('ws');
let server = http.createServer((request, response) => {
    /**
     * Receives POST parameter for notification
     */
    const getPostParam = (request, callback) => {
		const querystring = require('querystring')
        if (request.method === "POST") {
            let body = '';

            request.on('data', (data) => {
	            body += data;
	            if (body.length > 1e6) {
	                request.connection.destroy();
                }
	        });

	        request.on('end', () => {
				const POST = querystring.parse(body);
	            callback(POST);
	        });
        }
    }

    if (request.method === 'POST') {
        getPostParam(request, (POST) => {
			try {
                console.log(POST);
				notifyUser(JSON.parse(POST.data));
				response.writeHead(200);
			} catch (e) {
                console.error(e);
				response.writeHead(500);
			}
			response.end();
        })
        return;
    }
});

let notifClients = {};
let chatClients = {};
global.clients = {}; // store the connections

const wssNotif = new WebSocket.Server({
    noServer: true,
    perMessageDeflate: {
        zlibDeflateOptions: {
        // See zlib defaults.
        chunkSize: 1024,
        memLevel: 7,
        level: 3
        },
        zlibInflateOptions: {
        chunkSize: 10 * 1024
        },
        // Other options settable:
        clientNoContextTakeover: true, // Defaults to negotiated value.
        serverNoContextTakeover: true, // Defaults to negotiated value.
        serverMaxWindowBits: 10, // Defaults to negotiated value.
        // Below options specified as default values.
        concurrencyLimit: 10, // Limits zlib concurrency for perf.
        threshold: 1024 // Size (in bytes) below which messages
        // should not be compressed.
    }
});
const wssChat = new WebSocket.Server({
    noServer: true,
    perMessageDeflate: {
        zlibDeflateOptions: {
        // See zlib defaults.
        chunkSize: 1024,
        memLevel: 7,
        level: 3
        },
        zlibInflateOptions: {
        chunkSize: 10 * 1024
        },
        // Other options settable:
        clientNoContextTakeover: true, // Defaults to negotiated value.
        serverNoContextTakeover: true, // Defaults to negotiated value.
        serverMaxWindowBits: 10, // Defaults to negotiated value.
        // Below options specified as default values.
        concurrencyLimit: 10, // Limits zlib concurrency for perf.
        threshold: 1024 // Size (in bytes) below which messages
        // should not be compressed.
    }
});

wssNotif.on('connection', (ws, request, { id }) => {
    notifClients[Number(id)] = ws;
    ws.on('close', () => {
        delete notifClients.receiver_id;
    })
    ws.on('error', () => {
        delete notifClients.receiver_id;
    })
})

wssChat.on('connection', (ws, request, { id }) => {
    const receiver_id = Number(id);
    chatClients[receiver_id] = ws;
    ws.on('message', data => {
        messageUser(JSON.parse(data));
    })
    ws.on('close', () => {
        delete chatClients.receiver_id;
    })
    ws.on('error', () => {
        delete chatClients.receiver_id;
    })
})

server.on('upgrade', function upgrade(request, socket, head) {
    const pathname = url.parse(request.url).pathname;
    const { query } = queryString.parseUrl(request.url);

    if (pathname === '/') {
        wssNotif.handleUpgrade(request, socket, head, function done(ws) {
            wssNotif.emit('connection', ws, request, query);
        });
    } else if (pathname === '/chat') {
        wssChat.handleUpgrade(request, socket, head, function done(ws) {
            wssChat.emit('connection', ws, request, query);
        });
    } else {
        socket.destroy();
    }
});


server.listen(WebSocketServerPort, () => {
    console.log('Server is listening')
})
.on('error', (err) => {
    if (err.code === 'EADDRINUSE') console.log('Port is already in use.')
});

const notifyUser = (data) => {
    const receiver_id = Number(data.receiverId)
	if ( ! notifClients[receiver_id]) {
        return
	}
    if (notifClients[receiver_id].readyState !== notifClients[receiver_id].OPEN) {
        return;
    }
    notifClients[receiver_id].send(JSON.stringify(data))
}

const messageUser = (data) => {
    const receiver_id = Number(data.receiver_id);
    if ( ! chatClients[receiver_id]) return;

    if (chatClients[receiver_id].readyState !== chatClients[receiver_id].OPEN) {
        return;
    }
    chatClients[receiver_id].send(JSON.stringify(data));
}
