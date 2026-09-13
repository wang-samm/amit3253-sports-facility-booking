output "sns_topic_arn" {
  description = "SNS topic receiving CloudWatch alarm state changes."
  value       = aws_sns_topic.alerts.arn
}
