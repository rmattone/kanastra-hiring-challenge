import request from '../utilities/request'

export function list(data) {
  return request({
    url: '/charges',
    method: 'get',
    data
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
  

export function destroy(id) {
  return request({
    url: '/charges/' + id,
    method: 'delete'
  })
}

export function update(id, data) {
  return request({
    url: '/charges/' + id,
    method: 'put',
    data
  })
}
