import request from '../utilities/request.js'

export function list() {
  return request({
    url: '/charges',
    method: 'get'
  })
}

export function store(data) {
    return request({
      url: '/charges',
      method: 'post',
      data,
      headers: {
        'Content-Type': 'multipart/form-data',
      },
      timeout: 600000, // Increase the timeout to 600 seconds
    });
  }
