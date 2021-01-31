# Akeneo Messenger Bundle

This bundle provide the missing pieces to integrate [Symfony Messenger](https://symfony.com/doc/4.4/messenger.html) with the PIM.

## Messenger Transport for Google Pub/Sub

The Transport require the library ["google/cloud-pubsub"](https://packagist.org/packages/google/cloud-pubsub).

It follows the official Symfony documentation on [creating a custom Transport](https://symfony.com/doc/4.4/messenger/custom-transport.html).

The environment variable `SRNT_GOOGLE_APPLICATION_CREDENTIALS` must be defined with the file path of the JSON file that contains your [service account key](https://cloud.google.com/docs/authentication/getting-started#setting_the_environment_variable).

### Simple queue configuration

For a simple configuration of a Pub/Sub Topic with only one Subscription:

```yml
framework:
  messenger:
    transports:
      my_queue:
        dsn: 'gps:'
        options:
          project_id: '%env(GOOGLE_CLOUD_PROJECT)%'
          topic_name: '%env(PUBSUB_TOPIC)%'
          subscription_name: '%env(PUBSUB_SUBSCRIPTION)%'
        retry_strategy:
          max_retries: 0
        serializer: pim_enrich.messenger.serializer.business_event

    routing:
      'My\Event': my_queue
```

### Queue with multiple subscribers

Google Pub/Sub use a [subscription model](https://en.wikipedia.org/wiki/Publish%E2%80%93subscribe_pattern) and it means that one Topic can have more than one Subscription.

To be able to handle this, we recommends to have multiple transport definitions with one that serve as Producer only while the other ones are Consumers.

```yml
framework:
  messenger:
    transports:
      # Producer

      my_producer:
        dsn: 'gps:'
        options:
          project_id: '%env(GOOGLE_CLOUD_PROJECT)%'
          topic_name: '%env(PUBSUB_TOPIC)%'
        retry_strategy:
          max_retries: 0
        serializer: pim_enrich.messenger.serializer.business_event

      # Consumers

      my_first_consumer:
        dsn: 'gps:'
        options:
          project_id: '%env(GOOGLE_CLOUD_PROJECT)%'
          topic_name: '%env(PUBSUB_TOPIC)%'
          subscription_name: '%env(PUBSUB_SUBSCRIPTION_1)%'
        retry_strategy:
          max_retries: 0
        serializer: pim_enrich.messenger.serializer.business_event

      my_second_consumer:
        dsn: 'gps:'
        options:
          project_id: '%env(GOOGLE_CLOUD_PROJECT)%'
          topic_name: '%env(PUBSUB_TOPIC)%'
          subscription_name: '%env(PUBSUB_SUBSCRIPTION_2)%'
        retry_strategy:
          max_retries: 0
        serializer: pim_enrich.messenger.serializer.business_event

    routing:
      'My\Event': my_producer
```

From the Symfony Messenger point of view, this is three independant queues. But from Pub/Sub point of view, all messages sent to the producer will be dispatched to the consumers.

### Transport Options

- `topic_name: string`

- `subscription_name: ?string`

  Optional, but if the option is not defined you won't be able to receive messages from this Transport.

- `auto_setup: ?bool`

  Default to `false`, but can be enabled to make the Transport create the Topic and Subscription for you.
  This is useful when using the in-memory [Pub/Sub emulator](https://cloud.google.com/pubsub/docs/emulator) (enabled when the environment variable `PUBSUB_EMULATOR_HOST` is defined).

## Purge Command for the Doctrine Transport table

Define a Command to purge the table defined by the Doctrine Transport.

```sh
bin/console akeneo:messenger:doctrine:purge-messages --retention-time=7200 <table-name> <queue-name>
```

The goal is to be able to remove Messages that are to old (default to 2 hours).