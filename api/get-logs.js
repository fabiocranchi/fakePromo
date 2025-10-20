import { kv } from '@vercel/kv';

export default async function handler(req, res) {
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Content-Type', 'application/json');

  try {
    // Buscar todos os timestamps dos logs
    const logTimestamps = await kv.lrange('logs:all', 0, -1);
    
    // Buscar cada log individual
    const logs = [];
    for (const timestamp of logTimestamps) {
      const logEntry = await kv.get(`log:${timestamp}`);
      if (logEntry) {
        logs.push({
          timestamp: timestamp,
          ...logEntry
        });
      }
    }
    
    // Ordenar por timestamp (mais recente primeiro)
    logs.sort((a, b) => b.timestamp - a.timestamp);
    
    return res.status(200).json({
      success: true,
      total: logs.length,
      logs: logs
    });

  } catch (error) {
    console.error('Erro ao buscar logs:', error);
    return res.status(500).json({ error: 'Failed to retrieve logs' });
  }
}
