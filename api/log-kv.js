import { kv } from '@vercel/kv';

export default async function handler(req, res) {
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'POST');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

  if (req.method !== 'POST') {
    return res.status(405).json({ error: 'Method not allowed' });
  }

  try {
    const newEntry = req.body;
    
    // Gerar timestamp único
    const timestamp = Date.now();
    
    // Salvar no Vercel KV
    await kv.set(`log:${timestamp}`, newEntry);
    
    // Também manter uma lista de todos os logs
    await kv.lpush('logs:all', timestamp);
    
    return res.status(200).json({
      success: true,
      message: 'Log saved to Vercel KV',
      timestamp: timestamp
    });

  } catch (error) {
    console.error('Erro ao salvar no KV:', error);
    return res.status(500).json({ error: 'Failed to save log' });
  }
}
