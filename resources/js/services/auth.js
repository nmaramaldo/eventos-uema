// resources/js/services/auth.js

function getCsrfToken() {
  const t = document.querySelector('meta[name="csrf-token"]')?.content
  if (!t) throw new Error('CSRF token não encontrado no <meta name="csrf-token">')
  return t
}

/** LOGIN */
export async function login({ email, password, remember = false }) {
  const token = getCsrfToken()

  const res = await fetch('/login', {
    method: 'POST',
    credentials: 'same-origin', // envia cookie de sessão
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': token,
    },
    body: JSON.stringify({ email, password, remember }),
  })

  if (!res.ok) throw await res.json().catch(() => ({ message: 'Falha no login' }))
  return res
}

/** CADASTRO */
export async function register({ name, email, password, password_confirmation }) {
  const token = getCsrfToken()

  const res = await fetch('/register', {
    method: 'POST',
    credentials: 'same-origin',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': token,
    },
    body: JSON.stringify({ name, email, password, password_confirmation }),
  })

  if (!res.ok) throw await res.json().catch(() => ({ message: 'Falha no cadastro' }))
  return res
}

/** LOGOUT */
export async function logout() {
  const token = getCsrfToken()

  await fetch('/logout', {
    method: 'POST',
    credentials: 'same-origin',
    headers: {
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': token,
    },
  })
}
